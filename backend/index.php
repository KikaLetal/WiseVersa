<?php
define('BASE_PATH', __DIR__);

header("Content-Type: application/json");

// CORS
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once BASE_PATH . '/db/init.php';
require_once BASE_PATH . '/core/Router.php';

$router = new Router();
$router->handleRequest();
?>