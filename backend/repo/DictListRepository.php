<?php

class DictListRepository {
    public function getLists($userId) {

        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT id, name, source_lang, target_lang, type, icon
            FROM dict_lists
            WHERE user_id = :user_id
        ");

        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function getListById(int $listId): ?array {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT id, user_id, name, type, source_lang, target_lang, icon 
            FROM dict_lists 
            WHERE id = :list_id
        ");
        
        $stmt->execute(['list_id' => $listId]);
        $res = $stmt->fetch();
        
        return $res ?: null;
    }

    public function getItemsCount($listId) {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT id, user_id, name, type, source_lang, target_lang, icon 
            FROM dict_lists 
            WHERE id = :list_id
        ");
        
        $stmt->execute(['list_id' => $listId]);
        $res = $stmt->fetch();
        
        return $res ?: null;
    }

    public function createList($data) {
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO dict_lists (user_id, name, source_lang, target_lang, icon) 
            VALUES (:user_id, :name, :source_lang, :target_lang, :icon)
        ");

        $stmt->execute([
            'user_id'     => $data['user_id'], 
            'name'        => $data['name'], 
            'source_lang' => $data['source_lang'], 
            'target_lang' => $data['target_lang'],
            'icon'        => $data['icon']
        ]);

        return (int)$db->lastInsertId();
    }

    public function createSystemList($userId, $name, $type, $icon) {
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO dict_lists (user_id, name, type, source_lang, target_lang, icon) 
            VALUES (:user_id, :name, :type, NULL, NULL, :icon)
        ");
        
        $stmt->execute([
            'user_id' => $userId, 
            'name'    => $name, 
            'type'    => $type, 
            'icon'    => $icon
        ]);
        
        return (int)$db->lastInsertId();
    }

    public function updateIcon($listId, $icon) {
        $db = Database::connect();

        $stmt = $db->prepare("
            UPDATE dict_lists 
            SET icon = :icon 
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'icon' => $icon, 
            'id'   => $listId
        ]);
    }
}