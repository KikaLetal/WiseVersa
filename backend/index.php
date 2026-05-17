<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/core/cors.php';

try {
    require_once BASE_PATH . '/db/init.php';
    require_once BASE_PATH . '/core/Router.php';

    $router = new Router();
    $router->handleRequest();
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}
