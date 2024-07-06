<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/HandleServices/GlobalHandler.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/SecuredActioners/Account.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/SecuredActioners/AccountTMP.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/SecuredActioners/AccountOTP.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/Notifier.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/Authenticator.php';

class HandleDeleteAccount implements GlobalHandler {
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
        if (!isset($input['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email is required']);
            return;
        }

        // Récupère les données
        $email = $input['email'];
        
        // Instancie les classes nécessaires
        $authenticator = new Authenticator();
        $tempAccount = new AccountTMP();
        $otpAccount = new AccountOTP();
        $notifier = new Notifier();


        if (!$authenticator->isUserInDatabase($email) || $authenticator->isWaitingConfirmation($email, 'delete')) { // vérifie que l'utilisateur existe et n'est pas déjà en attente de confirmation de suppression
            if ($authenticator->isWaitingConfirmation($email, 'delete')) { 
                $otp = $otpAccount->getOtp($email);
                if (!$authenticator->verifyOTPValidity($email, $otp)) { // vérifie si l'OTP est toujours valide
                    http_response_code(409);
                    echo json_encode(['error' => 'OTP expired. Please send delete account request again.']);
                    // supprime le compte temporaire si l'OTP est expiré, afin que l'utilisateur puisse recommencer la procédure
                    $tempAccount->deleteAccountTMPByEmail($email);
                    return;
                }else {
                    http_response_code(409);
                    echo json_encode(['error' => 'User delete waiting confirmation, email sent again.']);
                    // renvoie l'email de confirmation de suppression
                    $notifier->sendAccountDelete($email, $otp);
                    return;
                }   
            }
            http_response_code(409);
            echo json_encode(['error' => 'User does not exist or waiting delete confirmation']);
            return;
        }
        else
        {
            // Crée un compte temporaire pour la suppression (associe l'email a un otp pour confirmer la suppression)
            if ($tempAccount->createAccountTMP($email, "", "")) {
                $otp = rand(100000, 999999);
                $otpAccount->setOTP($email, $otp, 'delete'); // enregistre l'otp pour la suppression

                http_response_code(201);
                echo json_encode(['success' => 'Account delete confirmation sent to your email.']);
                // envoie l'email de confirmation de suppression
                $notifier->sendAccountDelete($email, $otp);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to send delete account confirmation']);
            }
        }
       
    }
}
?>
