<?php

require_once 'guards/AuthGuard.php';
require_once 'guards/ValidationGuard.php';
require_once 'models/User.php';

class UsersController {
    private $authGuard;
    private $validationGuard;
    private $userModel;
    
    public function __construct(){
        $this->authGuard = new AuthGuard();
        $this->validationGuard = new ValidationGuard();
        $this->userModel = new User();
    }

    public function register(){
        $data = json_decode(file_get_contents('php://input'), true);

        $validation = $this->validationGuard->validateRegisterInput($data);
        if($validation !== true) {
            $this->sendResponse(['status' => false, 'errors' => $validation], 400);
            return;
        }

        $username = $this->validationGuard->sanitizeString($data['username']);
        $email = $this->validationGuard->sanitizeString($data['email']);
        $password = $data['password'];

        $result = $this->authGuard->register($username, $email, $password);

        $this->sendResponse($result, $result['code']);
    }

    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);

        $validation = $this->validationGuard->validateLoginInput($data);
        if($validation !== true){
            $this->sendResponse(['status' => false, 'errors' => $validation], 400);
            return;
        }

        $email = $this->validationGuard->sanitizeString($data['email']);
        $password = $data['password'];

        $result = $this->authGuard->login($email, $password);

        $this->sendResponse($result, $result['code']);
    }

    public function getProfile(){
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? str_replace('Bearer', '', $headers['Authorization']) : null;

        if(!$token) {
            $this->sendResponse(['status' => false, 'message' => 'token no proporcionado'], 401);
            return;
        }

        $payload = $this->authGuard->verifyToken($token);
        if(!$payload){
            $this->sendResponse(['status' => false, 'message' => 'token invalido o expirado'], 401);
            return;
        }

        $user = $this->userModel->find($payload['user_id']);
        if(!$user){
            $this->sendResponse(['status' => false, 'message' => 'Usuario no encontrado'], 404);
            return;
        }

        $this->sendResponse([
            'status' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ]
            ], 200);
    }

    private function sendResponse($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}