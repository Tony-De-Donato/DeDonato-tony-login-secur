<?php

require_once 'Database.php';

class AccountTMPDatabase extends Database {
    public function __construct() {
        parent::__construct();
    }

    // Crée un nouveau compte et hash le mot de passe
    public function createAccountTMP($email, $password) {
        $stmt = $this->create('accounttmp', ['password' => $password]);
        
        $guid = $this->read('accounttmp', $password, 'password');
        
        $stmt = $this->create('usertmp', ['email' => $email, 'tmp_guid' => $guid['tmp_guid']]);

        if ($this->getUserTMPByEmail($email)) {
            return true;
        }
        
    }

    // crée le salt pour le compte temporaire
    public function setSalt($guid, $salt) {
        $stmt = $this->create('tmpaccountsalt', ['tmp_guid' => $guid, 'salt' => $salt]);
    }

    // Récupère le salt par email du compte temporaire
    public function getSaltByEmail($email) {
        $guid = $this->read('usertmp', $email, 'email');
        $guid = $guid['tmp_guid'];
        $stmt = $this->read('tmpaccountsalt', $guid, 'tmp_guid');
        return $stmt;
    }

    // Récupère le salt par id du compte temporaire
    public function getSaltById($id) {
        $stmt = $this->read('tmpaccountsalt', $id, 'tmp_guid');
        return $stmt;
    }


    // Récupère un compte par id du compte temporaire
    public function getAccountTMPById($id) {
        $stmt = $this->read('accounttmp', $id, 'tmp_guid');
        return $stmt;
    }

    // Supprime un compte par id du compte temporaire
    public function deleteAccountTMPById($id) {
        $stmt = $this->delete('accounttmp', $id, 'tmp_guid');
        $stmt = $this->delete('usertmp', $id, 'tmp_guid');
        $stmt = $this->delete('tmpaccountsalt', $id, 'tmp_guid');
        return $stmt;
    }

    // Supprime un compte par email du compte temporaire
    public function deleteAccountTMPByEmail($email) {
        $guid = $this->read('usertmp', $email, 'email');
        $guid = $guid['tmp_guid'];
        $stmt = $this->deleteAccountTMPById($guid);
        return $stmt;
    }

    // Récupère l'utilisateur associé au compte temporaire par email
    public function getUserTMPByEmail($email) {
        $stmt = $this->read('usertmp', $email, 'email');
        return $stmt;
    }

    // Récupère l'utilisateur associé au compte temporaire par id
    public function getUserTMPById($id) {
        $stmt = $this->read('usertmp', $id, 'tmp_guid');
        return $stmt;
    }


}
