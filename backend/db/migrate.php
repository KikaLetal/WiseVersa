<?php

require_once 'Database.php';

function runMigrations() {
    $db = Database::connect();

    if ($db->connect_error) {
        die("DB connection failed: " . $db->connect_error);
    }

    $db->query("
        CREATE TABLE IF NOT EXISTS migrations(
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL UNIQUE,
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    );

    $files = scandir(__DIR__ . '/migrations');

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $stmt = $db->prepare("SELECT filename FROM migrations WHERE filename = ?");
        $stmt->bind_param("s", $file);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0){
            continue;
        }

        $sql = file_get_contents(__DIR__ . "/migrations/$file");
        
        if ($db->query($sql)){
            echo "Migration applied: $file\n";
        } else{
            die("Migration error in $file: " . $db->error);
        }

        $stmt = $db->prepare("INSERT INTO migrations (filename) VALUES (?)");
        $stmt->bind_param("s", $file);
        $stmt->execute();
    }   
}