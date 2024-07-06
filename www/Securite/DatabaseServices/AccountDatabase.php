<?php

require_once 'Database.php';

class AccountDatabase extends Database {
    public function __construct() {
        parent::__construct();
    }

    // crée un compte utilisateur (son mot de passe)
    public function createAccount($password) {
        $stmt = $this->create('account', ['password' => $password]);
        return $stmt;
    }

    // récuperer un compte utilisateur par son id
    public function getAccountById($id) {
        $stmt = $this->read('account', $id, 'guid');
        return $stmt;
    }

    // récuperer le guid utilisateur par son mot de passe
    public function getAccountByPassword($password) {
        $stmt = $this->read('account', $password, 'password');
        return $stmt;
    }

    // modifier le mot de passe d'un utilisateur
    public function updatePassword($guid, $password) {
        $stmt = $this->update('account', $guid, 'guid', ['password' => $password]);
        return $stmt;
    }

    // supprimer un compte utilisateur par son id
    public function deleteAccountById($id) {
        $stmt = $this->delete('account', $id, 'guid');
        return $stmt;
    }

    // récuperer l'utilisateur associé au compte par son email
    public function getUserById($guid) {
        $stmt = $this->read('user', $guid, 'guid');
        return $stmt;
    }

    // récuperer le salt associé à un compte par son email
     public function setSaltByEmail($email, $salt) {
        $guid = $this->read('user', $email, 'email');
        $guid = $guid['guid'];
        $stmt = $this->create('accountsalt', ['guid' => $guid, 'salt' => $salt]);
    }

    // créer un salt pour un compte utilisateur par son guid
    public function setSalt($guid, $salt) {
        $stmt = $this->create('accountsalt', ['guid' => $guid, 'salt' => $salt]);
    }

    // supprimer un salt par son id
    public function deleteSaltById($id) {
        $stmt = $this->delete('accountsalt', $id, 'guid');
        return $stmt;
    }

    // récuperer le salt associé à un compte par son email
    public function getSaltByEmail($email) {
        $guid = $this->read('usertmp', $email, 'email');
        $guid = $guid['guid'];
        $stmt = $this->read('accountsalt', $guid, 'guid');
        return $stmt;
    }

    // récuperer le salt associé à un compte par son guid
    public function getSaltById($id) {
        $stmt = $this->read('accountsalt', $id, 'guid');
        return $stmt;
    }

    // modifier le salt associé à un compte par son guid (dans le cadre d'un changement de mot de passe)
    public function modifySalt($guid, $salt) {
        $stmt = $this->update('accountsalt', $guid, 'guid', ['salt' => $salt]);
        return $stmt;
    }

    // supprimer les permissions d'un compte par son guid
    public function deleteAccountAuthorization($guid) {
        $stmt = $this->delete('accountauthorization', $guid, 'guid');
        return $stmt;
    }

    // Supprimer les tentatives de connexion d'un compte par son guid
    public function deleteAccountAttempts($guid) {
        $stmt = $this->delete('accountattempts', $guid, 'guid');
        return $stmt;
    }

    // récuperer les permissions d'un compte
    public function modifyAccountAuthorization($guid, $authorization) {
        $stmt = $this->update('accountauthorization', $guid, 'guid', ['authorization' => $authorization]);
        return $stmt;
    }

    // créer une tentative de connexion pour un compte
    public function createAccountAttempts($guid) {
        $stmt = $this->create('accountattempts', ['guid' => $guid, 'attempts' => 0]);
        return $stmt;
    }



}
