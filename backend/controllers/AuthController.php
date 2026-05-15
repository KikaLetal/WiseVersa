<?php
header("Content-Type: application/json");

require_once BASE_PATH . "/services/AuthService.php";
require_once BASE_PATH . "/middleware/AuthMiddleware.php";

class AuthController {

    public function register(){
        $data =[
            "username" => $_POST['username'] ?? null,
            "password" => $_POST['password'] ?? null,
            "avatar" => $_FILES['avatar'] ?? null,
        ];
        
        $service = new AuthService();

        $result = $service->register($data);

        if (isset($result['error'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => $result['error']
            ]);
            return;
        }

        echo json_encode([
            "success" => true,
            "token" => $result['token'],
            "user" => $result['user']
        ]);
    }

    public function login(){
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        $service = new AuthService();

        $result = $service->login($data);

        if (isset($result['error'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => $result['error']
            ]);
            return;
        }

        echo json_encode([
            "success" => true,
            "token" => $result['token'],
            "user" => $result['user']
        ]);
    }

    public function me(){
        $middleware = new AuthMiddleware();
        $payload = $middleware->handle();

        $service = new AuthService();

        $result = $service->meById($payload['id']);

        if (isset($result['error'])) {
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "error" => $result['error']
            ]);
            return;
        }

        echo json_encode([
            "success" => true,
            "user" => $result['user']
        ]);
        }

}