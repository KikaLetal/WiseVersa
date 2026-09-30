<?php
require_once BASE_PATH . '/controllers/AuthController.php';
require_once BASE_PATH . '/controllers/DictListController.php';
require_once BASE_PATH . '/controllers/DictItemsController.php';
require_once BASE_PATH . '/controllers/TranslatorController.php';
require_once BASE_PATH . '/controllers/BlogController.php';

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

            case '/translate':
                // POST /api/translate
                if ($method === 'POST') {
                    $controller = new TranslatorController();
                    $controller->translate();
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

            case '/dictionary/items':
                if ($method === 'POST') {
                    $controller = new DictItemsController();
                    $controller->addItem();
                    return;
                }
                break;
            case '/blog/posts':
                if ($method === 'GET') {
                    (new BlogController())->getPosts();
                    return;
                }
                if ($method === 'POST') {
                    (new BlogController())->createPost();
                    return;
                }
                break;
            
        }

        if ($method === 'GET' && preg_match('#^/dictionary/lists/(\d+)/items$#', $uri, $matches)) {
            (new DictItemsController())->getItems((int)$matches[1]);
            return;
        }

        if ($method === 'DELETE' && preg_match('#^/dictionary/lists/(\d+)/items/(\d+)$#', $uri, $matches)) {
            (new DictItemsController())->deleteItem((int)$matches[1], (int)$matches[2]);
            return;
        }

        if ($method === 'PATCH' && preg_match('#^/dictionary/items/(\d+)/master$#', $uri, $matches)) {
            (new DictItemsController())->updateMastered((int)$matches[1]);
            return;
        }

        if ($method === 'POST' && preg_match('#^/dictionary/lists/(\d+)/reset$#', $uri, $matches)) {
            (new DictItemsController())->resetProgress((int)$matches[1]);
            return;
        }

        if ($method === 'GET' && preg_match('#^/blog/posts/(\d+)$#', $uri, $matches)) {
            (new BlogController())->getPost((int)$matches[1]);
            return;
        }

        if ($method === 'PUT' && preg_match('#^/blog/posts/(\d+)$#', $uri, $matches)) {
            (new BlogController())->updatePost((int)$matches[1]);
            return;
        }

        if ($method === 'DELETE' && preg_match('#^/blog/posts/(\d+)$#', $uri, $matches)) {
            (new BlogController())->deletePost((int)$matches[1]);
            return;
        }

        if ($method === 'POST' && preg_match('#^/blog/posts/(\d+)/like$#', $uri, $matches)) {
            (new BlogController())->toggleLike((int)$matches[1]);
            return;
        }

        if ($method === 'POST' && preg_match('#^/blog/posts/(\d+)$#', $uri, $matches)) {
            if (isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
                (new BlogController())->updatePost((int)$matches[1]);
                return;
            }
        }

        http_response_code(404);

        echo json_encode(['success' => false, 'error' => 'Endpoint not found']);
    }
}