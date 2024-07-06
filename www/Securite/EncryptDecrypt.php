<?php
class EncryptDecrypt {

    // Générer un sel unique
    private function generateSalt($length = 16) {
        return bin2hex(random_bytes($length));
    }

    // Hacher un mot de passe avec salage et étirement
    public function hashPassword($password) {
        $salt = $this->generateSalt();
        $saltedPassword = $salt . $password;

        // Utiliser une fonction de dérivation de clé pour étirer le mot de passe
        $hashedPassword = hash_pbkdf2("sha256", $saltedPassword, $salt, 10000, 64);

        // Retourner le hachage et le sel séparément
        return ['salt' => $salt, 'hashedPassword' => $hashedPassword];
    }

    // Vérifier un mot de passe par rapport à un hachage
    public function verifyPassword($password, $salt, $hashedPassword) {
        $saltedPassword = $salt . $password;
        $verifyHash = hash_pbkdf2("sha256", $saltedPassword, $salt, 10000, 64);

        return hash_equals($hashedPassword, $verifyHash);
    }
}
