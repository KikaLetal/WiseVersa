<?php
require_once BASE_PATH . '/db/Database.php';

class DictItemRepository {
    private PDO $db;
    public function __construct() {
        $this->db = Database::connect();
    }

    public function addItem(int $listId, string $word, string $translation): ?int {
        $stmt = $this->db->prepare("
            INSERT INTO dict_items (list_id, word, translation)
            VALUES (:list_id, :word, :translation)
        ");

        $res = $stmt->execute([
            'list_id'     => $listId,
            'word'        => $word,
            'translation' => $translation
        ]);
        
        return $res ? (int)$this->db->lastInsertId() : null;
    }

    public function deleteItem(int $userId, int $listId, int $itemId): bool {
        $stmt = $this->db->prepare("
            DELETE FROM dict_items di
            USING dict_lists dl
            WHERE di.list_id = dl.id 
              AND di.id = :item_id 
              AND dl.id = :list_id 
              AND dl.user_id = :user_id
        ");
        
        $stmt->execute([
            'item_id' => $itemId,
            'list_id' => $listId,
            'user_id' => $userId
        ]);
        
        return $stmt->rowCount() > 0;
    }

    public function exists(int $listId, string $word, string $translation): bool {
        $stmt = $this->db->prepare("
            SELECT id FROM dict_items
            WHERE list_id = :list_id AND word = :word AND translation = :translation
            LIMIT 1
        ");
        
        $stmt->execute([
            'list_id'     => $listId,
            'word'        => $word,
            'translation' => $translation
        ]);
        
        return (bool)$stmt->fetch();
    }

    public function getItems(int $listId): array {
        $stmt = $this->db->prepare("
            SELECT id, word, translation, created_at, mastered
            FROM dict_items
            WHERE list_id = :list_id
            ORDER BY created_at DESC
        ");
        
        $stmt->execute(['list_id' => $listId]);
        
        return $stmt->fetchAll();
    }

    public function updateMastered(int $itemId, bool $mastered, int $userId): bool {
        $stmt =  $this->db->prepare("
            UPDATE dict_items di
            SET mastered = :mastered
            FROM dict_lists dl 
            WHERE di.list_id = dl.id 
              AND di.id = :item_id 
              AND dl.user_id = :user_id
        ");
        
        return $stmt->execute([
            'mastered' => $mastered ? 1 : 0,
            'item_id'  => $itemId,
            'user_id'  => $userId
        ]);
    }

    public function resetMasteredForList(int $listId, int $userId): bool {
        $stmt = $this->db->prepare("
            UPDATE dict_items
            SET mastered = 0
            WHERE list_id = :list_id 
              AND list_id IN (SELECT id FROM dict_lists WHERE user_id = :user_id)
        ");
        
        return $stmt->execute([
            'list_id' => $listId,
            'user_id' => $userId
        ]);
    }
}