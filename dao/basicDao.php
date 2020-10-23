<?php

require_once 'database.php';

abstract class GenericDao {
	
	private $db;
	private $dbh;
	private $reflection;
	
	function __construct() {
		$this->db = new DatabaseConnection(Config::host, Config::port, Config::database, Config::user, Config::password);
		$this->dbh = $this->db->getDbh();

		$this->reflection = new ReflectionClass($this->getEntityClass());
	}
	
	public function findAll() {
		$query = "SELECT * FROM ".strtolower($this->getEntityClass()).";";
		$stmt = $this->dbh->prepare($query);
		if ($stmt->execute()) {
			$result = [];
			while ($row = $stmt->fetch()) {
				array_push($result, $this->mapToEntity($row));
			}
			return $result;
		}
		return [];
	}
	
	public function findById($id) {
		$query = "SELECT * FROM ".strtolower($this->getEntityClass())." WHERE id = :id;";
		$stmt = $this->dbh->prepare($query);
		$stmt->bindParam(':id', $id);
		if ($stmt->execute()) {
			while ($row = $stmt->fetch()) {
				return $this->mapToEntity($row);
			}
		}
		return null;
	}
	
	public function insert($entity) {
		$query = "INSERT INTO ".strtolower($this->getEntityClass())." (";
		$fields = $this->getFields();
		for ($i = 0; $i < sizeof($fields); $i++) {
			if ($fields[$i] != $this->getIdField()) {
				$query = $query.$fields[$i];
				if ($i < sizeof($fields) - 1) {
					$query = $query.", ";
				}			
			}
		}

		$query = $query.") VALUES (";
		for ($i = 0; $i < sizeof($fields); $i++) {
			if ($fields[$i] != $this->getIdField()) {
				$query = $query."?";
				if ($i < sizeof($fields) - 1) {
					$query = $query.", ";
				}
			}
		}
		$query = $query.");";

		$values = [];
		for ($i = 0; $i < sizeof($fields); $i++) {
			if ($fields[$i] != $this->getIdField()) {
				array_push($values, $entity->{$fields[$i]});
			}
		}

		$this->dbh->prepare($query)->execute($values);
	}
	
	public function update($entity) {
		$query = "UPDATE ".strtolower($this->getEntityClass())." SET ";
		$fields = $this->getFields();
		for ($i = 0; $i < sizeof($fields); $i++) {
			if ($fields[$i] != $this->getIdField()) {
				$query = $query.$fields[$i]." = ?";
				if ($i < sizeof($fields) - 1) {
					$query = $query.", ";
				}
			}
		}
		$query = $query." WHERE ".$this->getIdField()." = ?".";";

		$values = [];
		for ($i = 0; $i < sizeof($fields); $i++) {
			if ($fields[$i] != $this->getIdField()) {
				array_push($values, $entity->{$fields[$i]});
			}
		}
		array_push($values, $entity->{$this->getIdField()});

		$this->dbh->prepare($query)->execute($values);
	}

	public function delete($id) {
		$query = "DELETE FROM ".strtolower($this->getEntityClass())." WHERE id = ?;";
		$values = [$id];
		$this->dbh->prepare($query)->execute($values);
	}
	
	abstract protected function getEntityClass();
	
	protected function getFields() {
		$properties = $this->reflection->getProperties(ReflectionProperty::IS_PRIVATE);
		$fields = [];
		foreach ($properties as $prop) {
			array_push($fields, $prop->getName());
		}
		return $fields;
	}

	protected function mapToEntity($row) {
		$fields = $this->getFields();
		$properties = [];
		foreach ($fields as $field) {
			$properties[$field->getName()] = $row[$field->getName()];
		}
		return $this->reflection->newInstanceArgs($properties);
	}
	
	protected function getIdField() {
		return "id";
	}
	
}
