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
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getItemsCount($listId) {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT COUNT(*) as count
            FROM dict_items
            WHERE list_id = :list_id
        ");

        $stmt->execute(['list_id' => $listId]);
        $row = $stmt->fetch();

        return (int)($row['count'] ?? 0); 
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

        return $db->lastInsertId();
    }

    public function createSystemList($userId, $name, $type, $icon) {
        $db = Database::connect();
        
        $sourceLang = 'ru';
        $targetLang = 'en';

        $stmt = $db->prepare("
            INSERT INTO dict_lists (user_id, name, type, source_lang, target_lang, icon) 
            VALUES (:user_id, :name, :type, :source_lang, :target_lang, :icon)
        ");
        
        $stmt->execute([
            'user_id'     => $userId,
            'name'        => $name,
            'type'        => $type,
            'source_lang' => $sourceLang,
            'target_lang' => $targetLang,
            'icon'        => $icon
        ]);
        
        return $db->lastInsertId();
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