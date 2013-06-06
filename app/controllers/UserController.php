<?php
class UserController extends Simps_Controller {
	
	public function indexAction () {
		$users = new Users($this->db);
		$this->view->users = $users->getUsers();
	}
	
	public function registerAction () {
		$this->view->myVariable = "myValue";
	}
	
}