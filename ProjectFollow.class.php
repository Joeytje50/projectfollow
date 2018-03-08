<?php 

class ProjectFollow {
	public function locateUsersInList($listId) {
		$userIds = $this->getList($listId)->getUsers();

		return array_map(function($userId) {
			return User::newFromId($userId);
		}, $userIds);
	}
}