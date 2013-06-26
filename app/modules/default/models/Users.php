<?php
class Users {
	
	protected $db;
	
	public function __construct($db) {
		$this->db = $db;
	}
	
	public function getUsers () {
		return $this->db->query("SELECT * FROM users")->fetchAll();
	}
	
}