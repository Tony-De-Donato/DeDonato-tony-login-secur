<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Securite/HandleServices/GlobalHandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Securite/SecuredActioners/AccountTMP.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Securite/SecuredActioners/AccountOTP.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Securite/Notifier.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Securite/Authenticator.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Securite/EncryptDecrypt.php';

class HandlePasswordChange implements GlobalHandler {
    public function handle($method) {
        // Vérifie si la méthode est POST
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        // Récupère les données de la requête
        $input = json_decode(file_get_contents('php://input'), true);

        // Vérifie si les données que l'on traite ensuite sont bien présentes
        if (!isset($input['email']) || !isset($input['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and password are required']);
            return;
        }

        // Récupère les données
        $email = $input['email'];
        $password = $input['password'];

        // Instancie les classes nécessaires
        $authenticator = new Authenticator();
        $tempAccount = new AccountTMP();
        $otpAccount = new AccountOTP();
        $notifier = new Notifier();


        if (!$authenticator->isUserInDatabase($email) || $authenticator->isWaitingConfirmation($email, 'password_change')) { // vérifie que l'utilisateur existe et n'est pas déjà en attente de confirmation de changement de mot de passe
            if ($authenticator->isWaitingConfirmation($email, 'password_change')) {
                $otp = $otpAccount->getOtp($email);
                if (!$authenticator->verifyOTPValidity($email, $otp)) { // vérifie si l'OTP est toujours valide
                    http_response_code(409);
                    echo json_encode(['error' => 'OTP expired. Please send password change request again.']);
                    // supprime le compte temporaire si l'OTP est expiré, afin que l'utilisateur puisse recommencer la procédure
                    $tempAccount->deleteAccountTMPByEmail($email);
                    return;
                }else {
                    http_response_code(409);
                    echo json_encode(['error' => 'User password change waiting confirmation, email sent again.']);
                    // renvoie l'email de confirmation de changement de mot de passe
                    $notifier->sendPasswordReset($email, $otp);
                    return;
                }   
            }
            http_response_code(409);
            echo json_encode(['error' => 'User does not exist or waiting password change confirmation']);
            return;
        }
        else
        {
            // Instancie la classe servant a encrypter le mot de passe
            $encrypter = new EncryptDecrypt();
            $hashedPassword = $encrypter->hashPassword($password); // hash le mot de passe et renvoie séparement le mot de passe (hashé et salé) et le salt
            $salt = $hashedPassword['salt'];
            $hashedPassword = $hashedPassword['hashedPassword'];
            if ($tempAccount->createAccountTMP($email, $hashedPassword, $salt)) { // crée un compte temporaire avec le mot de passe hashé et le salt associé
                $otp = rand(100000, 999999);
                $otpAccount->setOTP($email, $otp, 'password_change'); // enregistre l'otp pour le changement de mot de passe

                http_response_code(201);
                echo json_encode(['success' => 'Password change confirmation sent to your email.']);
                // envoie l'email de confirmation de changement de mot de passe
                $notifier->sendSignUpConfirmation($email, $otp);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to send password change confirmation']);
            }
        }
       
    }
}
?>
