<?php
require_once BASE_PATH . '/controllers/AuthController.php';
require_once BASE_PATH . '/controllers/DictListController.php';

class Router {
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        switch ($uri) {
            case '/auth/ping':
                if ($method === 'GET') {
                    echo json_encode(['success' => true, 'ok' => true]);
                    return;
                }
                break;

            case '/auth/register':
                // POST /api/auth/register
                if($method === 'POST') {
                    $controller = new AuthController();
                    $controller->register();
                    return;
                }
                break;

            case '/auth/login':
                // POST /api/auth/login
                if($method === 'POST') {
                    $controller = new AuthController();
                    $controller->login();
                    return;
                }
                break;

            case '/auth/me':
                // GET /api/auth/me
                if ($method === 'GET') {
                    $controller = new AuthController();
                    $controller->me();
                    return;
                }
                break;

            case '/dictionary/lists':
                // GET /api/dictionary/lists
                if ($method === 'GET') {
                    $controller = new DictListController();
                    $controller->getLists();

                    return;
                } 

                
                // POST /api/dictionary/lists
                if ($method === 'POST') {
                    $controller = new DictListController();
                    $controller->createList();

                    return;
                }
                break;
        }

        http_response_code(404);

        echo json_encode(['success' => false, 'error' => 'Endpoint not found']);
    }
}