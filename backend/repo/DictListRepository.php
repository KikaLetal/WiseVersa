<?php

class DictListRepository {
    public function getLists($userId) {

        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT id, name, source_lang, target_lang, type, icon
            FROM dict_lists
            WHERE user_id = ?
        ");

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getItemsCount($listId) {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT COUNT(*) as count
            FROM dict_items
            WHERE list_id = ?
        ");

        $stmt->bind_param("i", $listId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return (int)($row['count'] ?? 0); 
    }

    public function createList($data) {
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO dict_lists (user_id, name, source_lang, target_lang, icon) 
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "issss", 
            $data['user_id'], 
            $data['name'], 
            $data['source_lang'], 
            $data['target_lang'],
            $data['icon']
        );

        $stmt->execute();

        return $db->insert_id;
    }

    public function createSystemList($userId, $name, $type, $icon) {
        $db = Database::connect();
        
        $sourceLang = 'ru';
        $targetLang = 'en';

        $stmt = $db->prepare("
            INSERT INTO dict_lists (user_id, name, type, source_lang, target_lang, icon) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param("isssss", $userId, $name, $type, $sourceLang, $targetLang, $icon);
        $stmt->execute();
        
        return $db->insert_id;
    }

    public function updateIcon($listId, $icon) {
        $db = Database::connect();

        $stmt = $db->prepare("
            UPDATE dict_lists 
            SET icon = ? 
            WHERE id = ?
        ");

        $stmt->bind_param("si", $icon, $listId);
        
        return $stmt->execute();
    }
}