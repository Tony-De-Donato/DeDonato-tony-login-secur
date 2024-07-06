<?php
require_once 'Database.php';

class AccountAuthorization {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Attribue une autorisation à un compte
    public function grantAuthorization($email, $webService, $permission) {
        $stmt = $this->db->getConnection()->prepare('INSERT INTO AccountAuthorization (email, webService, permission) VALUES (:email, :webService, :permission)');
        return $stmt->execute([
            'email' => $email,
            'webService' => $webService,
            'permission' => $permission
        ]);
    }

    // Vérifie l'autorisation d'un compte
    public function checkAuthorization($email, $webService) {
        $stmt = $this->db->getConnection()->prepare('SELECT permission FROM AccountAuthorization WHERE email = :email AND webService = :webService');
        $stmt->execute([
            'email' => $email,
            'webService' => $webService
        ]);
        return $stmt->fetch();
    }

    // Révoque une autorisation d'un compte
    public function revokeAuthorization($email, $webService) {
        $stmt = $this->db->getConnection()->prepare('DELETE FROM AccountAuthorization WHERE email = :email AND webService = :webService');
        return $stmt->execute([
            'email' => $email,
            'webService' => $webService
        ]);
    }
}
?>
