<?php
require_once $_SERVER['DOCUMENT_ROOT']."/Securite/DatabaseServices/UserDatabase.php";
require_once $_SERVER['DOCUMENT_ROOT']."/Securite/DatabaseServices/AccountTMPDatabase.php";
require_once $_SERVER['DOCUMENT_ROOT']."/Securite/DatabaseServices/OtpDatabase.php";
require_once $_SERVER['DOCUMENT_ROOT']."/Securite/SecuredActioners/Account.php";
require_once $_SERVER['DOCUMENT_ROOT']."/Securite/EncryptDecrypt.php";
require_once $_SERVER['DOCUMENT_ROOT']."/Configs/Configs.php";



class Authenticator {
    private $db;
    private $otpValidity;
    private $configsGetter;

    public function __construct() {
        // Récupère la durée de validité d'un OTP
        $this->configsGetter = new Configs();
        $this->otpValidity = $this->configsGetter->GetConfigs('configs.json')->otpValidTime;
    }

    // Vérifie le mot de passe d'un utilisateur 
    public function authenticate($email, $password) {
        $account = new Account();
        $hashedPassword = $account->getAccountByEmail($email)['password'];
        $salt = $account->getSalt($email)['salt'];
        $encryptDecrypt = new EncryptDecrypt();
        
        if ($encryptDecrypt->verifyPassword($password, $salt, $hashedPassword)) {
            return true;
        }
        return false;        
    }

    // Vérifie si un utilisateur est dans la base de données
    public function isUserInDatabase($email) {
        $this->db = new UserDatabase();
        $user = $this->db->getUserByEmail($email);
        if ($user) {
            return true;
        }
        return false;
    }

    // Vérifie si un utilisateur est dans la base de données temporaire (pour l'action en attente de confirmation)
    public function isWaitingConfirmation($email, $action) {
        $this->db = new AccountTMPDatabase();
        $user = $this->db->getUserTMPByEmail($email);
        $this->db = new OtpDatabase();
        $otp = $this->db->getOtpByEmail($email);   

        if ($user && $otp['action'] == $action) {
            return true;
        }
        return false;
    }

    // Vérifie si un otp est encore valide
    public function verifyOTPValidity($email, $otp) {
        $this->db = new OtpDatabase();
        $otpData = $this->db->getOtpByEmail($email);

        if ($otpData) {
            $otpTime = strtotime($otpData['created_at']);
            $currentTime = time();

            if (($otpData['otp'] == $otp) && ($currentTime - $otpTime < $this->otpValidity)) {
                return true;
            }
        }

        return false;
    }
}  
?>
