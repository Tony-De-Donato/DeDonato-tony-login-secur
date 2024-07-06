<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/HandleServices/GlobalHandler.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/SecuredActioners/AccountTMP.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/SecuredActioners/Account.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/SecuredActioners/AccountOTP.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/Notifier.php';

class HandleConfirmationDelete implements GlobalHandler {
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
        if (!isset($input['email']) || !isset($input['otp'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and OTP are required']);
            return;
        }

        // Récupère les données
        $email = $input['email'];
        $otp = $input['otp'];

        // Instancie les classes nécessaires
        $tempAccount = new AccountTMP();
        $otpAccount = new AccountOTP();
        $account = new Account();
        $notifier = new Notifier();

        
        if ($otpAccount->verifyOTP($email, $otp, 'delete')) { // Vérifie si l'email est bien présent dans la base de données
            if ($otpAccount->verifyOTPValidity($email, $otp)) { // Vérifie si l'OTP est toujours valide
                $tempGuid = $tempAccount->getUserByEmail($email)['tmp_guid'];
                if ($tempAccount->deleteAccountTMPByEmail($email) && $account->deleteAllAccountByEmail($email)) { // Supprime le compte temporaire et le compte utilisateur
                    http_response_code(201); 
                    echo json_encode(['success' => 'Account deleted']); 
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to delete account']);
                }
            } else {
                http_response_code(409);
                echo json_encode(['error' => 'OTP expired. Please send account deletion request again']);
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
