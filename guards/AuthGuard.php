<?php

require_once 'core/DB.php';
require_once 'guards/EncryptionGuard.php';
require_once 'models/User.php';

class AuthGuard {
    private $db;
    private $encryption;
    private $userModel;

    public function __construct()
    {
        $this->db = new db();
        $this->encryption = new EncryptionGuard();
        $this->userModel = new User();
    }

    public function register($username, $email, $password){
        if($this->userModel->exists($username, $email)){
            return [
                'status' => false,
                'message' => 'El usuario o email ya esta registrado',
                'code' => 409
            ];
        }

        $hashedPassword = $this->encryption->hashPassword($password);

        $userId = $this->userModel->create($username, $email, $hashedPassword);

        if(!$userId){
            return [
                'status' => false,
                'message' => 'Error al registrar usuario',
                'code' => 500
            ];
        }

        return [
            'status' => true,
            'message' => 'Usuario registrado exitosamente',
            'code' => 201
        ];
    }

    public function login($username, $password) {
        $user = $this->userModel->findByUserNameOrEmail($username);

        if(!$user){
            return [
                'status' => false,
                'message' => 'credenciales invalidas',
                'code' => 401
            ];
        }

        if (!$this->encryption->verifyPassword($password, $user['password'])) {
            return [
                'status' => false,
                'message' => 'credenciales invalidas',
                'code' => 401
            ];
        }

        $token = $this->generateToken($user);

        return [
            'status' => true,
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ],
            'code' => 200
        ];
    }

    public function verifyToken($token) {
        $tokenParts = explode('.', $token);
        if(count($tokenParts) != 3) {
            return false;
        }

        $payload = json_decode(base64_decode($tokenParts[1]), true);

        if(!isset($payload['exp']) || $payload['exp'] < time()) {
            return false;
        }

        if(!isset($payload['user_id'])) {
            return false;
        }

        return $payload;
    }

    private function generateToken($user){
        $header = [
            'alg' => 'hs256',
            'typ' => 'JWT'
        ];

        $payload = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'iat' => time(),
            'exp' => time() + (60*60*24)
        ];

        $headerEncoded = base64_encode(json_encode($header));
        $payloadEncoded = base64_encode(json_encode($payload));

        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", 'tu_clave_secreta');
        $signatureEncoded = base64_encode($signature);

        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }
}