<?php

require_once BASE_PATH . '/services/JWTService.php';

class AuthMiddleware {
    public function handle() {
        $headers = getallheaders();

        $authHeader =
            $headers['Authorization']
            ?? $headers['authorization']
            ?? null;

        if (!$authHeader) {
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "error" => "No token"
            ]);
            exit;
        }

        $token = str_replace("Bearer ", "", $authHeader);

        $jwtService = new JWTService();
        $payload = $jwtService->validateToken($token);

        if (!$payload) {
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "error" => "Invalid token"
            ]);
            exit;
        }

        return $payload;
    }
}