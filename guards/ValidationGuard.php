<?php

class ValidationGuard {
    public function validateEmail($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validatePassword($password){
        return strlen($password) >= 8 && 
        preg_match('/[A-Z]/', $password) &&
        preg_match('/[a-z]/', $password) &&
        preg_match('/[0-9]/', $password);
    }

    public function sanitizeString($string){
        return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
    }

    public function validateLoginInput($data){
       $errors = [];
       
       if (empty($data['email'])){
        $errors['email'] = 'El nombre de usuario es requerido';
       }

       if(empty($data['password'])){
        $errors['password'] = 'La contraseña es requerida';
       }

       return empty($errors) ? true : $errors;
    }

    public function validateRegisterInput($data){
        $errors = [];

        if(empty($data['username'])){
            $errors['username'] = 'El nombre de usuario es requerido';
        } elseif (strlen($data['username']) < 3){
            $errors['username'] = 'El nombre de usuario debe tener al menos 3 caracteres';
        }

        if(empty($data['email'])){
            $errors['email'] = 'El email es requerido';
        } elseif (!$this->validateEmail($data['email'])){
            $errors['email'] = 'EL email no es valido';
        }

        if(empty($data['password'])){
            $errors['password'] = 'La contraseña es requerida';
        } elseif(!$this->validatePassword($data['password'])){
            $errors['password'] = 'Lacontraseña debe tener al menos 8 caracteres, una letra mayuscula, una minuscula y un numero';
        }

        if(empty($data['confirm_password'])){
            $errors['confirm_password'] = 'Debe confirmar la contraseña';
        } elseif ($data['password'] !== $data['confirm_password']){
            $errors['confirm_password'] = 'Las contraseñas no coinciden';
        }

        return empty($errors) ? true : $errors;
    }
}