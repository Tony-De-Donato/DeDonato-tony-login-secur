<?php

require_once $_SERVER['DOCUMENT_ROOT']."/Securite/DatabaseServices/Database.php";

class OtpDatabase extends Database {
    public function __construct() {
        parent::__construct();
    }

    // Crée un otp pour le compte temporaire
    public function createOtp($guid, $email, $otp, $action) {
        $stmt = $this->create('accountotp', ['tmp_guid' => $guid, 'email' => $email, 'otp' => $otp, 'action' => $action]);
        return $stmt;
    }

    // Récupère un compte temporaire par email
    public function getOtpByEmail($email) {
        $stmt = $this->read('accountotp', $email, 'email');
        return $stmt;
    }

    // Supprime un compte temporaire par email
    public function deleteOtpByEmail($email) {
        $stmt = $this->delete('accountotp', $email, 'email');
        return $stmt;
    }
}
