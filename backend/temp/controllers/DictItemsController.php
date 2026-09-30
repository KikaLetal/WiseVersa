<?php
require_once BASE_PATH . '/services/DictItemService.php';
require_once BASE_PATH . '/middleware/AuthMiddleware.php';

class DictItemsController{
    private DictItemService $service;

    public function __construct() {
        $this->service = new DictItemService();
    }

    public function getItems($listId) {
        $auth = new AuthMiddleware();
        $payload = $auth->handle();
        $userId = $payload['id'];

        $listRepo = new DictListRepository();
        $list = $listRepo->getListById($listId);
        if (!$list || $list['user_id'] !== $userId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            return;
        }

        $items = $this->service->getListItems($listId);
        echo json_encode(['success' => true, 'data' => $items]);
    }

    public function addItem() {
        $auth = new AuthMiddleware();
        $payload = $auth->handle();
        $userId = $payload['id'];

        $data = json_decode(file_get_contents('php://input'), true);
        $listId = $data['list_id'] ?? 0;
        $word = $data['word'] ?? '';
        $translation = $data['translation'] ?? '';
        $allowDuplicate = $data['allow_duplicate'] ?? false;

        if (!$listId || !$word || !$translation) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing parameters']);
            return;
        }

        $result = $this->service->addItemToDictList($userId, $listId, $word, $translation, $allowDuplicate);

        if (isset($result['error'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $result['error']]);
            return;
        }

        echo json_encode(['success' => true, 'id' => $result['id']]);
    }

    public function deleteItem($listId, $itemId) {
        $middleware = new AuthMiddleware();
        $payload = $middleware->handle();

        $userId = $payload['id'];

        $result = $this->service->deleteItem($userId, $listId, $itemId);

        if (!$result) {
            http_response_code(400);
            echo json_encode(['success'=> false, 'error' => 'Failed to delete item']);
            return;
        }

        echo json_encode(['success' => true]);
    }

    public function updateMastered($itemId) {
        $auth = new AuthMiddleware();
        $payload = $auth->handle();
        $data = json_decode(file_get_contents('php://input'), true);
        $mastered = $data['mastered'] ?? false;
        $result = $this->service->setMastered((int)$itemId, $payload['id'], $mastered);
        if (!$result) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Failed to update']);
            return;
        }
        echo json_encode(['success' => true]);
    }

    public function resetProgress($listId) {
        $auth = new AuthMiddleware();
        $payload = $auth->handle();
        $result = $this->service->resetListProgress($listId, $payload['id']);
        echo json_encode(['success' => $result]);
    }
}