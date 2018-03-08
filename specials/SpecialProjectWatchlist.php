<?php
/**
 * projectWatchlist SpecialPage for ProjectFollow extension
 *
 * @file
 * @ingroup Extensions
 */
class SpecialProjectWatchlist extends SpecialPage {
	public function __construct() {
		parent::__construct( 'projectWatchlist' );
	}

	/**
	 * Show the page to the user
	 *
	 * @param string $sub The subpage string argument (if any).
	 */
	public function execute( $sub ) {
		$out = $this->getOutput();
		$out->setPageTitle( $this->msg( 'special-projectWatchlist-title' ) );
		$out->addHelpLink( 'How to become a MediaWiki hacker' );
		$out->addWikiMsg( 'special-projectWatchlist-intro' );
	}

	protected function getGroupName() {
		return 'other';
	}
}
