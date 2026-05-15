<?php

require_once BASE_PATH . '/db/Database.php';

class UserRepository {
    public function getUserByUsername($username) {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT * 
            FROM users 
            WHERE username = ?
        ");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getUserById($id) {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT * 
            FROM users 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function createUser($data){
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO users (
                username,
                password_hash,
                profile_picture
            )
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("sss",
            $data['username'], 
            $data['password_hash'], 
            $data['profile_picture']
        );

        $stmt->execute();

        return $db->insert_id;
    }
}