<?php

class User implements JsonSerializable {

	private $id;
	private $firstName;
	private $lastName;

	function __construct($id, $firstName, $lastName) {
		$this->id = $id;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
	}

	public function jsonSerialize() {
		return (object) get_object_vars($this);
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getFirstName() {
		return $this->firstName;
	}

	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	public function getLastName() {
		return $this->lastName;
	}

	public function setLastName() {
		$this->lastName = lastName;
	}

}
