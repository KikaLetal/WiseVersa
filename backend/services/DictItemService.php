<?php
require_once BASE_PATH . '/repo/DictItemRepository.php';
require_once BASE_PATH . '/repo/DictListRepository.php';

class DictItemService {
    private DictItemRepository $itemRepo;
    private DictListRepository $listRepo;

    public function __construct() {
        $this->itemRepo = new DictItemRepository();
        $this->listRepo = new DictListRepository();
    }   

    public function getListItems(int $listId): array {
        return $this->itemRepo->getItems($listId);
    }

    public function addItemToDictList(
        int $userId, 
        int $listId, 
        string $word, 
        string $translation, 
        bool $allowDuplicate = false): array {
        
        $list = $this->listRepo->getListById($listId);
        if (!$list || $list['user_id'] !== $userId) {
            return ['error' => 'List not found or access denied'];
        }

        if (!$allowDuplicate && $this->itemRepo->exists($listId, $word, $translation)) {
            return ['error' => 'Word already exists in this list'];
        }

        $id = $this->itemRepo->addItem($listId, $word, $translation);
        if ($id === null) {
            return ['error' => 'Failed to save word'];
        }

        return ['success' => true, 'id' => $id];
    }

    public function deleteItem(int $userId, int $listId, int $itemId): bool {
        return $this->itemRepo->deleteItem($userId, $listId, $itemId);
    }

    public function setMastered(int $itemId, int $userId, bool $mastered): bool {
        return $this->itemRepo->updateMastered($itemId, $mastered, $userId);
    }

    public function resetListProgress(int $listId, int $userId): bool {
        return $this->itemRepo->resetMasteredForList($listId, $userId);
    }
}