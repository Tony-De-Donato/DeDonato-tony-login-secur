<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/HandleServices/GlobalHandler.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/SecuredActioners/AccountTMP.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/SecuredActioners/Account.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/SecuredActioners/AccountOTP.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/Notifier.php';

class HandleConfirmationSignUp implements GlobalHandler {
    public function handle($method) {
        // vérifie si la méthode est POST
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        // récupère les données de la requête
        $input = json_decode(file_get_contents('php://input'), true);

        // vérifie si les données que l'on traite ensuite sont bien présentes
        if (!isset($input['email']) || !isset($input['otp'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and OTP are required']);
            return;
        }

        // récupère les données
        $email = $input['email'];
        $otp = $input['otp'];

        // instancie les classes nécessaires
        $tempAccount = new AccountTMP();
        $otpAccount = new AccountOTP();
        $notifier = new Notifier();


        if ($otpAccount->verifyOTP($email, $otp, 'signup')) {// vérifie si un otp a bien été créé pour une demande d'inscription pour l'email
            if ($otpAccount->verifyOTPValidity($email, $otp)) { // vérifie si l'OTP est toujours valide
                $tempGuid = $tempAccount->getUserByEmail($email)['tmp_guid'];
                if ($tempAccount->validateAccountTMP($tempGuid)) { // valide le compte temporaire (crée le compte utilisateur et supprime le compte temporaire)
                    http_response_code(201);
                    echo json_encode(['success' => 'Account confirmed successfully. You can now log in.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to confirm account']);
                }
            } else {
                http_response_code(409);
                echo json_encode(['error' => 'OTP expired. Please sign up again.']);
                // supprime le compte temporaire si l'OTP est expiré, afin que l'utilisateur puisse recommencer la procédure
                $tempAccount->deleteAccountTMPByEmail($email);
            }
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid OTP']);
        }
    }
}
?>
