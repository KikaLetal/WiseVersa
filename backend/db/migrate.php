<?php

require_once 'Database.php';

function runMigrations() {
    $db = Database::connect();

    $db->exec("
        CREATE TABLE IF NOT EXISTS migrations(
            id SERIAL PRIMARY KEY,
            filename VARCHAR(255) NOT NULL UNIQUE,
            applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    );

    $files = scandir(__DIR__ . '/migrations');

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $stmt = $db->prepare("SELECT filename FROM migrations WHERE filename = :filename");
        $stmt->execute(['filename' => $file]);

        if ($stmt->fetch()) {
            continue;
        }

        $sql = file_get_contents(__DIR__ . "/migrations/$file");

        try{
            $db->exec($sql);
            error_log("Migration applied: $file");
        } catch (PDOException $e) {
            throw new RuntimeException("Migration error in $file: " . $e->getMessage());
        }

        $stmt = $db->prepare("INSERT INTO migrations (filename) VALUES (:filename)");
        $stmt->execute(['filename' => $file]);
    }   
}