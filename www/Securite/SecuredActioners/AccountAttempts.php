<?php
require_once 'Database.php';

class AccountAttempts {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Enregistre une tentative de connexion
    public function logAttempt($email) {
        $stmt = $this->db->getConnection()->prepare('INSERT INTO AccountAttempts (email, attempt_time) VALUES (:email, :attempt_time)');
        $stmt->execute([
            'email' => $email,
            'attempt_time' => time()
        ]);
    }

    // Vérifie si l'utilisateur est verrouillé
    public function isLockedOut($email, $maxAttempts, $lockoutTime) {
        $stmt = $this->db->getConnection()->prepare('SELECT COUNT(*) AS attempt_count FROM AccountAttempts WHERE email = :email AND attempt_time > :time_limit');
        $stmt->execute([
            'email' => $email,
            'time_limit' => time() - $lockoutTime
        ]);

        $attempt = $stmt->fetch();
        return $attempt['attempt_count'] >= $maxAttempts;
    }

    // Nettoie les tentatives de connexion anciennes
    public function clearAttempts($email) {
        $stmt = $this->db->getConnection()->prepare('DELETE FROM AccountAttempts WHERE email = :email');
        $stmt->execute(['email' => $email]);
    }
}
?>
