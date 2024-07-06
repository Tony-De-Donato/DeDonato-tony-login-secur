<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/DataBaseServices/DataBase.php';

class UserDatabase extends Database {
    public function __construct() {
        parent::__construct();
    }

    // Crée un nouvel user
    public function createUser($id, $email){
        $stmt = $this->create('user', ['guid' => $id, 'email' => $email]);
    }

    // Récupère un user par email
    public function getUserByEmail($email) {
        $stmt = $this->read('user', $email, 'email');
        return $stmt;
    }

    // Récupère un user par id
    public function getUserById($id) {
        $stmt = $this->read('user', $id, 'guid');
    }

    // Supprime un user par email
    public function deleteUserByEmail($email) {
        $stmt = $this->delete('user', $email, 'email');
    }

    // Supprime un user par id
    public function deleteUserById($id) {
        $stmt = $this->delete('user', $id, 'guid');
    }

}