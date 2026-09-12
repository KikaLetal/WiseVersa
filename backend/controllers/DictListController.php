<?php
require_once BASE_PATH . '/services/DictListService.php';
require_once BASE_PATH . '/middleware/AuthMiddleware.php';

class DictListController {

    public function getLists() {
        $middleware =new AuthMiddleware();
        $payload = $middleware->handle();

        $userId = $payload['id'];

        $service = new DictListService();
        $lists = $service->getLists($userId);

        echo json_encode([
            "success" => true,
            "data" => $lists
        ]);
    }

    public function createList() {
        $middleware = new AuthMiddleware();
        $payload = $middleware->handle();

        $data = json_decode(file_get_contents('php://input'), true);
        $data['user_id'] = $payload['id'];

        $service = new DictListService();
        $result = $service->createList($data);

        if (isset($result["error"])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => $result["error"]
            ]);
            return;
        }

        echo json_encode(["success" => true, "data" => $result]);
    }

    public function updateIcon($listId) {
        $middleware = new AuthMiddleware();
        $payload = $middleware->handle();

        if (!isset($_FILES['icon'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => "Icon file is required"
            ]);
            return;
        }

        $service = new DictListService();
        $result = $service->updateListIcon($listId, $_FILES['icon']);

        if (isset($result["error"])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => $result["error"]
            ]);
            return;
        }

        echo json_encode([
            "success" => true,
            "message" => "Icon updated successfully"
        ]);
    }
}