<?php

require_once 'basicDao.php';

class UserDao extends GenericDao {

	protected function getEntityClass() {
        return 'User';
    }

}