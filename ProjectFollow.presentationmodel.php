<?php
class EchoProjectFollowPresentationModel extends EchoEventPresentationModel {
	public function canRender() {
	    // Define that we have to have the page this is
	    // refering to as a condition to display this
	    // notification
		return (bool)$this->event->getTitle();
	}
	public function getIconType() {
	    // You can use existing icons in Echo icon folder
	    // or define your own through the $icons variable
	    // when you define your event in BeforeCreateEchoEvent
		return 'project';
	}
	public function getHeaderMessage() {
		if ( $this->isBundled() ) {
		    // This is the header message for the bundle that contains
		    // several notifications of this type
			$msg = $this->msg( 'notification-bundle-projectfollow-edit' );
			$msg->params( $this->getBundleCount() );
			$msg->params( $this->getTruncatedTitleText( $this->event->getTitle(), true ) );
			$msg->params( $this->getViewingUserForGender() );
			return $msg;
		} else {
		    // This is the header message for individual non-bundle message
			$msg = $this->getMessageWithAgent( 'notification-myext-projectfollow-edit' );
			$msg->params( $this->getTruncatedTitleText( $this->event->getTitle(), true ) );
			$msg->params( $this->getViewingUserForGender() );
			return $msg;
		}
	}
	//TODO:
	public function getCompactHeaderMessage() {
	    // This is the header message for individual notifications
	    // *inside* the bundle
		$msg = parent::getCompactHeaderMessage();
		$msg->params( $this->getViewingUserForGender() );
		return $msg;
	}
	public function getBodyMessage() {
	    // This is the body message. We will add the edit summary
		$comment = $this->getRevisionEditSummary();
		if ( $comment ) {
			$msg = new RawMessage( '$1' );
			$msg->plaintextParams( $comment );
			return $msg;
		}
	}
	
	public function getPrimaryLink() {
	    // This is the link to the new page
	    $link = $this->getPageLink( $this->event->getTitle(), null, true );
        return $link;
    }

	public function getSecondaryLinks() {
		if ( $this->isBundled() ) {
		    // For the bundle, we don't need secondary actions
			return [];
		} else {
		    // For individual items, display a link to the user
		    // that created this page
			return [ $this->getAgentLink() ];
		}
	}
}
