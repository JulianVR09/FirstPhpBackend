<?php

class EncryptionGuard {
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function generateRamdomToken($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    public function encrypt($data, $key){
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    public function decrypt($data, $key){
        $data = base64_decode($data);
        list($encrypted_data, $iv) = explode('::', $data, 2);
        return openssl_decrypt($encrypted_data, 'aes-256', $key, 0, $iv);
    }
}