<?php

require_once 'DB.php';

function initializeDataBase(){
    $db = new db();
    
    $db->createTable("users",[
        "id" => "INT AUTO_INCREMENT PRIMARY KEY",
        "username" => "VARCHAR(100) NOT NULL",
        "email" => "VARCHAR(255) UNIQUE NOT NULL",
        "password" => "VARCHAR(255) NOT NULL",
        "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
    ]);
}