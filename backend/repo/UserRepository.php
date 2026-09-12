<?php

require_once BASE_PATH . '/db/Database.php';

class UserRepository {
    public function getUserByUsername($username) {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT * 
            FROM users 
            WHERE username = :username
        ");
        $stmt->execute(['username' => $username]);

        return $stmt->fetch();
    }

    public function getUserById($id) {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT * 
            FROM users 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function createUser($data){
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO users (
                username,
                password_hash,
                profile_picture
            )
            VALUES (:username, :password_hash, :profile_picture)
        ");
        $stmt->execute([
            'username'        => $data['username'], 
            'password_hash'   => $data['password_hash'], 
            'profile_picture' => $data['profile_picture']
        ]);

        return $db->lastInsertId();
    }

    public function beginTransaction() {
        Database::beginTransaction();
    }

    public function commit() {
        Database::commit();
    }

    public function rollback() {
        Database::rollback();
    }
}