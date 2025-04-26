<?php

require 'DB.php';
require 'models/User.php';
require 'controller/UsersController.php';
require 'Tables.php';

class Core {
    public function run(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        initializeDataBase();

        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            exit(0);
        }

        $uri = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));

        $controller = isset($uri[0]) && !empty($uri[0]) ? $uri[0] : 'defaultController';
        $action = isset($uri[1]) && !empty($uri[1]) ? $uri[1] : 'defaultAction';

        $this->handleRequest($controller, $action);
    }

    private function handleRequest($controller, $action){
        switch($controller){
            case 'users':
                $usersController = new UsersController();
                switch($action){
                    case 'register':
                        $usersController->register();
                        break;
                    
                    case 'login':
                        $usersController->login();
                        break;

                    case 'profile':
                        $usersController->getProfile();
                        break;
                    
                    default:
                        header('HTTP/1.1 404 Not Found');
                        echo json_encode(['status' => false, 'message' => 'Ruta no encontrada']);
                        break;
                }
            break;

            default:
                $this->sendErrorResponse('Controlador no valido', 400);
        }
    }

    private function sendErrorResponse($message, $statusCode){
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode(['status' => false, 'message' => $message]);
        exit;
    }
}