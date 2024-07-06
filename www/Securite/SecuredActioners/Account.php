<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/DatabaseServices/AccountDatabase.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/DatabaseServices/UserDatabase.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/EncryptDecrypt.php';

class Account {
    private $db;
    private $encryptDecrypt;


    // Crée un nouveau compte utilisateur
    public function createAccount($email, $password) {
        $this->db = new AccountDatabase();
        $stmt = $this->db->createAccount($password);
    }

    // Récupère les informations de l'utilisateur par email
    public function getAccountByEmail($email) {
        $this->db = new UserDatabase();
        $guid = $this->db->getUserByEmail($email)['guid'];
        $this->db = new AccountDatabase();
        $stmt = $this->db->getAccountById($guid);
        return $stmt;
    }

    // Met à jour le mot de passe de l'utilisateur
    public function updatePassword($email, $newPassword) {
        $encryptDecrypt = new EncryptDecrypt();
        $hashedPassword = $encryptDecrypt->hashPassword($newPassword);
        $this->db = new UserDatabase();
        $guid = $this->db->getUserByEmail($email)['guid'];
        $this->db = new AccountDatabase();
        $this->db->updatePassword($guid, $hashedPassword['hashedPassword']);
        $this->db->setSaltBy($guid, $hashedPassword['salt']);

    }

    // Met à jour le sel de l'utilisateur
    public function setSalt($email, $salt) {
        $this->db = new AccountDatabase();
        $this->db->setSaltByEmail($email, $salt);
    }

    // Récupère le sel de l'utilisateur
    public function getSalt($email) {
        $this->db = new UserDatabase();
        $guid = $this->db->getUserByEmail($email)['guid'];
        $this->db = new AccountDatabase();
        $stmt = $this->db->getSaltById($guid);
        return $stmt;
    }

    // Supprime le compte et l'utilisateur associé
    public function deleteAllAccountByEmail($email) {
        $this->db = new UserDatabase();
        $guid = $this->db->getUserByEmail($email)['guid'];
        $this->db->deleteUserByEmail($email);
        $this->db = new AccountDatabase();
        $this->db->deleteAccountById($guid);
        $this->db->deleteSaltById($guid);
        $this->db->deleteAccountAuthorization($guid);
        $this->db->deleteAccountAttempts($guid);
        $stmt = $this->db->getAccountById($guid);
        if (!$stmt) {
            return true;
        }
        return false;
    }
}
?>
