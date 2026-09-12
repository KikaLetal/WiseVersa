<?php

class Database {
    private static $instance = null;
    public static function connect(){
        if (self::$instance === null) {
            $host = getenv('BACKEND_HOST') ?: 'postgres';
            $user = getenv('DB_USER') ?: 'postgres';
            $password = getenv('DB_PASSWORD') ?: '';
            $database = getenv('DB_NAME') ?: '';
            $port = '5432';

            try {
                $dsn = "pgsql:host=$host;port=$port;dbname=$database";

                self::$instance = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                throw new RuntimeException("DB connection failed: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    public static function beginTransaction() {
        self::connect()->beginTransaction();
    }

    public static function commit() {
        self::connect()->commit();
    }

    public static function rollback() {
        self::connect()->rollback();
    }
}
