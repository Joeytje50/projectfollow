<?php
/**
 * ProjectFollow extension hooks
 *
 * @file
 * @ingroup Extensions
 * @license GPL-3.0+
 */
class ProjectFollowHooks {
	/**
	 * Occurs after the save page request has been processed.
	 *
	 * @param WikiPage $wikiPage
	 * @param User $user
	 * @param Content $content
	 * @param string $summary
	 * @param boolean $isMinor
	 * @param boolean $isWatch Deprecated
	 * @param $section Deprecated
	 * @param integer $flags
	 * @param {Revision|null} $revision
	 * @param Status $status
	 * @param integer $baseRevId
	 * @param integer $undidRevId
	 *
	 * @return boolean
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/PageContentSaveComplete
	 */
	public function onPageContentSaveComplete(
	    $wikiPage, $user, $content, $summary, $isMinor, $isWatch, $section, $flags,
	    $revision, $status, $baseRevId, $undidRevId
	) {
		// First: do not show bot edits to anyone.
		if (in_array('bot', $user->getRights())) return true;

		$users = self::getFollowingUsers($wikiPage, $user);

		$ctx = new RequestContext();

		// create echo event based on https://www.mediawiki.org/wiki/Extension:Echo/Creating_a_new_notification_type
		// and https://www.mediawiki.org/wiki/Notifications/Developer_guide#Hook_the_notification_into_the_Echo_extension
		$echo = EchoEvent::create([
			'type' => 'projectfollow-edit',
			'title' => $wikiPage->getTitle(),
			'extra' => [
				'revid' => $revision->getId(),
				'source' => 'page',
				'excerpt' => EchoDiscussionParser::getEditExcerpt( $revision, $ctx->getLanguage() ),
				'recipients' => $users,
			],
			'agent' => $user,
		]);
		return true;
	}

	public static function onEchoGetDefaultNotifiedUsers( $event, &$users ) {
		switch ( $event->getType() ) {
			case 'projectfollow-edit':
				$extra = $event->getExtra();
				if ( !$extra || !isset( $extra['recipients'] ) ) {
					break;
				}
				foreach ($extra->recipients as $u) {
					$uid = $u->getId();
					$users[$uid] = $u;
				}
				break;
		}
		return true;
	}

	/**
	 * Add Project events to Echo
	 *
	 * @param $notifications array of Echo notifications
	 * @param $notificationCategories array of Echo notification categories
	 * @param $icons array of icon details
	 * @return bool
	 */
	public function onBeforeCreateEchoEvent(&$notifications, &$notificationCategories, &$icons) {
		$notificationCategories['projectfollow-edit'] = [
			'priority' => 3,
			'tooltip' => 'echo-pref-tooltip-projectfollow-edit',
		];

		$notifications['projectfollow-edit'] = [
			'category' => 'projectfollow-edit',
			'group' => 'positive',
			'section' => 'alert',
			'presentation-model' => EchoProjectFollowPresentationModel::class,
			'bundle' => [
				'web' => true,
				'expandable' => true,
			],
		];
		$icons['projectfollow-edit'] = [
			'path' => [
				'ltr' => 'ProjectFollow/edit-ltr.svg',
				'rtl' => 'ProjectFollow/edit-rtl.svg'
			]
		];
	}

	/**
	 * Obtains list of users to notify
	 *
	 * @param WikiPage $wikipage
	 * @param User $editor
	 *
	 * @return array
	 */
	private function getFollowingUsers($wikiPage, $editor) {
		$users = array();
		$titles = self::getTitleList($wikiPage);
		// Code taken from CategoryWatch extension:
		// https://github.com/OrganicDesign/extensions/blob/master/MediaWiki-Legacy/CategoryWatch/CategoryWatch.php#L149
		$dbr = wfGetDB(DB_SLAVE);
		$dbr->IngoreErrors = true;
		$conds = 'wl_user <> ' . intval($editor->getId()) . ' AND ';
		foreach ($titles as $t) {
			$conds .= "(wl_title = \"{$t->getDBkey()}\" AND wl_namespace = \"{$t->getNamespace()}\") OR ";
		}
		$conds = substr($conds, 0, -4); //trim the last " OR"
		try {
			$res = $dbr->select( 'watchlist', 'wl_user', $conds);
		} catch (Exception $e) {
			echo "<pre>";
			echo $e;
			echo "</pre>";
			return array();
        }

		while ($row = $dbr->fetchRow($res)) {
			$users[] = User::newFromId($row[0]);
		}
		$dbr->freeResult($res);
		return $users;
	}

	/**
	 * Checks if the page needs to be added to the watchlist
	 *
	 * @param WikiPage $wikipage
	 *
	 * @return array
	 */
	private function getTitleList($wikiPage) {
		//TODO: generate title list
		return array($wikiPage->getTitle());
	}
}
