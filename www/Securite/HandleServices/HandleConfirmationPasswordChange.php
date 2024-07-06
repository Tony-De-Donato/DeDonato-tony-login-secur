<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/HandleServices/GlobalHandler.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/SecuredActioners/AccountTMP.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/SecuredActioners/Account.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/SecuredActioners/AccountOTP.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/Notifier.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/Authenticator.php';

class HandleConfirmationPasswordChange implements GlobalHandler {
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
        if (!isset($input['email']) || !isset($input['otp'])){
            http_response_code(400);
            echo json_encode(['error' => 'Email and otp are required']);
            return;
        }

        // Récupère les données
        $email = $input['email'];
        $otp = $input['otp'];

        // Instancie les classes nécessaires
        $authenticator = new Authenticator();
        $tempAccount = new AccountTMP();
        $otpAccount = new AccountOTP();
        $account = new Account();

        if ($otpAccount->verifyOTP($email, $otp, 'password_change')) { // Vérifie si un otp a bien été créé pour une demande de changement de mot passe pour l'email
            if ($otpAccount->verifyOTPValidity($email, $otp)) { // Vérifie si l'OTP est toujours valide
                $tempGuid = $tempAccount->getUserByEmail($email)['tmp_guid'];
                if ($tempAccount->applyPasswordChange($email)){ // Applique le changement de mot de passe (supprime le compte temporaire et met à jour le mot de passe de l'utilisateur)
                    http_response_code(201);
                    echo json_encode(['success' => 'Password changed successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to change password']);
                }
            } else {
                http_response_code(409);
                echo json_encode(['error' => 'OTP expired. Please send password change request again.']);
                // Supprime le compte temporaire si l'OTP est expiré, afin que l'utilisateur puisse recommencer la procédure
                $tempAccount->deleteAccountTMPByEmail($email);
            }
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid OTP']);
        }
    }
}
?>
