<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
header('Content-type: application/json');

require_once '../entity/user.php';
require_once '../dao/userDao.php';
require_once '../config.php';
require_once 'entityController.php';

$controller = new EntityController(new UserDao());
$controller->handleRequests();
