<?php

class DictListRepository {
    public function getLists($userId) {

        $db = Database::connect();

        $stmt = $db->query("
            SELECT id, name, source_lang, target_lang
            FROM dict_lists
            WHERE user_id = ?
        ");

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createList($data) {
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO dict_lists (user_id, name, source_lang, target_lang) 
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isss", 
            $data['user_id'], 
            $data['name'], 
            $data['source_lang'], 
            $data['target_lang']
        );

        $stmt->execute();

        return $db->insert_id;
    }

    public function createSystemList($userId, $name, $type) {
        $db = Database::connect();
        
        $stmt = $db->prepare("
            INSERT INTO dict_lists (user_id, name, type) 
            VALUES (?, ?, ?)
        ");
        
        $stmt->bind_param("iss", $userId, $name, $type);
        $stmt->execute();
        
        return $db->insert_id;
    }
}