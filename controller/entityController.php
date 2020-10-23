<?php

class EntityController {

    private $dao;

    function __construct($dao) {
        $this->dao = $dao;
    }

    public function handleRequests() {
        if (isset($_GET['findAll']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
            echo json_encode($this->dao->findAll());
        }
        
        if (isset($_GET['findById']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
            echo json_encode($this->dao->findById($_GET['findById']));
        }

        if (isset($_GET['save']) && $_SERVER['REQUEST_METHOD'] === 'PUT') {
			$body = file_get_contents("php://input");
        	$event = json_decode($body);
        	$this->dao->insert($event);
        }
        
        if (isset($_GET['update']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$body = file_get_contents("php://input");
        	$event = json_decode($body);
        	$this->dao->update($event);
        }
        
        if (isset($_GET['delete']) && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
			$this->dao->delete($_GET['delete']);
		}
    }

}
