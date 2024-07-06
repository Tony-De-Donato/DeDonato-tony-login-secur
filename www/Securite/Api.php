<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/HandleServices/HandleSignUp.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/HandleServices/HandleSignIn.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/HandleServices/HandleDeleteAccount.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/HandleServices/HandlePasswordChange.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/HandleServices/HandleConfirmationSignUp.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/HandleServices/HandleConfirmationDelete.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Securite/HandleServices/HandleConfirmationPasswordChange.php';

class Api {
    // Tableau contenant les endpoints et les handlers associés
    private $handlers = [];

    // Enregistre un handler pour un endpoint
    public function registerHandler($endpoint, $handler) {
        $this->handlers[$endpoint] = $handler;
    }

    // Gère la requête entrante
    public function handleRequest() {
        // Récupère les informations de la requête
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Définit le type de contenu de la réponse
        header('Content-Type: application/json');

        // Vérifie si un handler est enregistré pour cet endpoint
        if (isset($this->handlers[$requestUri])) {
            $handler = $this->handlers[$requestUri];
            $handler->handle($requestMethod);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Handler not found for this endpoint']);
        }
    }
}

// Crée une instance de l'API et enregistre les handlers
$api = new API();
$api->registerHandler('/Securite/SignUp/', new HandleSignUp());
$api->registerHandler('/Securite/SignIn/', new HandleSignIn());
$api->registerHandler('/Securite/DeleteAccount/', new HandleDeleteAccount());
$api->registerHandler('/Securite/ChangePassword/', new HandlePasswordChange());
$api->registerHandler('/Securite/ConfirmSignUp/', new HandleConfirmationSignUp());
$api->registerHandler('/Securite/ConfirmDelete/', new HandleConfirmationDelete());
$api->registerHandler('/Securite/ConfirmPasswordChange/', new HandleConfirmationPasswordChange());

// Gère la requête
$api->handleRequest();
?>
