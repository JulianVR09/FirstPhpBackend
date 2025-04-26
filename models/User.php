<?php

require_once 'core/DB.php';

class User extends db {
    public function __construct()
    {
        parent::__construct();
    }

    public function find($id){
        $sql = "SELECT * FROM users WHERE id= :id";
        $query = $this->prepare($sql);
        $query->execute(['id' => $id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function findByUserNameOrEmail($username) {
        $sql = "SELECT * FROM users WHERE username = :username OR email = :email";
        $query = $this->prepare($sql);
        $query->execute(['username' => $username, 'email' => $username]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function exists($username, $email){
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username OR email = :email";
        $query = $this->prepare($sql);
        $query->execute(['username' => $username, 'email' => $email]);
        return $query->fetchColumn() > 0;
    }

    public function create($username, $email, $password){
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $query = $this->prepare($sql);
        $result = $query->execute([
            'username' => $username,
            'email' => $email,
            'password' => $password
        ]);

        return $result ? $this->lastInsertId() : false;
    }

    public function update($id, $data){
        $fields = [];
        $values = ['id' => $id];

        foreach($data as $key => $value){
            $fields[] = "$key = :$key";
            $values[$key] = $value;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . "WHERE id = :id";
        $query = $this->prepare($sql);
        return $query->execute($values);
    }
}