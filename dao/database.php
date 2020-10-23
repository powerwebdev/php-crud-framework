<?php

class DatabaseConnection {

	private $dbh;
	private $result;
	
	private $host;
	private $port;
	private $database;
	private $user;
	private $password;
	
	function __construct($host, $port, $database, $user, $password) {
		$this->host = $host;
		$this->port = $port;
		$this->database = $database;
		$this->user = $user;
		$this->password = $password;

		$this->connect();
	}

	function connect() {
		$connectionString = "mysql:host=".$this->host.";port=".$this->port.";charset=utf8;dbname=".$this->database;
		$this->dbh = new PDO($connectionString,
			$this->user,
			$this->password);	
	}

	function getDbh() {
		return $this->dbh;
	}
	
}

?>