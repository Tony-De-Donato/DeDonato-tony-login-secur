<?php
require_once $_SERVER['DOCUMENT_ROOT']."/Securite/DatabaseServices/AccountTMPDatabase.php";
require_once $_SERVER['DOCUMENT_ROOT']."/Securite/DatabaseServices/UserDatabase.php";
require_once $_SERVER['DOCUMENT_ROOT']."/Securite/DatabaseServices/AccountDatabase.php";
require_once $_SERVER['DOCUMENT_ROOT']."/Securite/DatabaseServices/OtpDatabase.php";
require_once $_SERVER['DOCUMENT_ROOT']."/Securite/DatabaseServices/PermissionDatabase.php";

class AccountTMP {
    private $db;

    // Crée un compte temporaire
    public function createAccountTMP($email, $password, $salt) {
        $this->db = new AccountTMPDatabase();
        $this->db->createAccountTMP($email, $password);
        $guid = $this->db->getUserTMPByEmail($email)['tmp_guid'];
        $this->db->setSalt($guid, $salt);
        if ($this->db->getUserTMPByEmail($email)) {
            return true;
        }
    }

    // Supprime un compte temporaire par email et l'otp associé
    public function deleteAccountTMPByEmail($email) {
        $this->db = new AccountTMPDatabase();
        $this->db->deleteAccountTMPByEmail($email);
        $this->db = new OtpDatabase();
        $this->db->deleteOtpByEmail($email);
        $stmt = $this->db->getOtpByEmail($email);
        if (!$stmt) {
            return true;
        }
        return false;
    }

    // crée un compte utilisateur à partir d'un compte temporaire
    public function validateAccountTMP($guid) {
        $this->db = new AccountTMPDatabase();
        $account = $this->db->getAccountTMPById($guid);
        $email = $this->db->getUserTMPById($guid)['email'];
        $salt = $this->db->getSaltById($guid)['salt'];
        $this->db = new AccountDatabase();
        $this->db->createAccount($account['password']);
        $guid = $this->db->getAccountByPassword($account['password'])['guid'];
        $this->db->setSalt($guid, $salt);
        $this->db = new UserDatabase();
        $this->db->createUser($guid, $email);
        $this->db = new PermissionDatabase();
        $this->db->createPermission($guid, 1, 'user');

        $this->deleteAccountTMPByEmail($email);

        $this->db = new AccountDatabase();
        if ($this->db->getUserById($guid)) {
            return true;
        }
    }

    // Récupère un utilisateur par email
    public function getUserByEmail($email) {
        $this->db = new AccountTMPDatabase();
        return $this->db->getUserTMPByEmail($email);
    }

    // modifie le mot de passe d'un compte utilisateur à partir d'un compte temporaire lié à l'otp de changement de mot de passe
    public function applyPasswordChange($email) {
        $this->db = new UserDatabase();
        $guid = $this->db->getUserByEmail($email)['guid'];
        $this->db = new AccountTMPDatabase();
        $tpm_guid = $this->db->getUserTMPByEmail($email)['tmp_guid'];
        $salt = $this->db->getSaltByEmail($email)['salt'];
        $password = $this->db->getAccountTMPById($tpm_guid)['password'];
        $this->db = new AccountDatabase();
        $this->db->modifySalt($guid, $salt);
        $this->db->updatePassword($guid, $password);

        $this->deleteAccountTMPByEmail($email);

        $this->db = new AccountDatabase();
        if ($this->db->getAccountById($guid)['password'] == $password) {
            return true;
        }
        return false;
    }
   
}
?>
