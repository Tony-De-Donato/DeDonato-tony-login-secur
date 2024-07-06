<?php
require_once 'GlobalHandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Securite/Authenticator.php';

class HandleSignIn implements GlobalHandler {
    public function handle($method) {
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['email']) || !isset($input['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and password are required']);
            return;
        }

        $email = $input['email'];
        $password = $input['password'];

        $authenticator = new Authenticator();
        $account = new Account();


        if (!$authenticator->isUserInDatabase($email)) {
            http_response_code(401);
            echo json_encode(['error' => 'User not found']);
            return;
        } else {
            if ($authenticator->authenticate($email, $password)) {
                http_response_code(200);
                echo json_encode(['message' => 'User authenticated']);
                return;
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid credentials']);
                return;
            }
        }
    }
}
?>
