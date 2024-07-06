<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/DatabaseServices/OtpDatabase.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/DatabaseServices/AccountTMPDatabase.php';
require_once $_SERVER['DOCUMENT_ROOT']."/Configs/Configs.php";

class AccountOTP {
    private $db;
    private $otpValidity;
    private $configsGetter;

    public function __construct() {
        // Récupère la durée de validité d'un OTP
        $this->configsGetter = new Configs();
        $this->otpValidity = $this->configsGetter->GetConfigs('configs.json')->otpValidTime;
    
    }

    // Génère un OTP pour un compte
    public function setOtp($email, $otp, $action) {
        $this->db = new AccountTMPDatabase();
        $guid = $this->db->getUserTMPByEmail($email)['tmp_guid'];

        $this->db = new OtpDatabase();
        $this->db->createOtp($guid, $email, $otp, $action);

        if ($this->db->getOtpByEmail($email)) {
            return true;
        }
    }

    public function getOtp($email) {
        $this->db = new OtpDatabase();
        return $this->db->getOtpByEmail($email)['otp'];
    }

   

    // Vérifie si un OTP est correcte
    public function verifyOTP($email, $otp, $action) {
        $this->db = new OtpDatabase();
        $otpData = $this->db->getOtpByEmail($email);

        if ($otpData) {
            
                if ($otpData['otp'] == $otp && $otpData['action'] == $action) {
                    return true;
                }
            
        }
        return false;
    }

    // Vérifie si un OTP est valide
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
