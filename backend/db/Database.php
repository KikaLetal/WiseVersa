<?php

class Database {
    private static $instance = null;
    public static function connect(){
        if (self::$instance === null) {
            self::$instance = new mysqli(
                "***",
                '***',
                "***",
                "***"
            );

            if(self::$instance->connect_error) {
                throw new RuntimeException(
                    "DB connection failed: " . self::$instance->connect_error
                );
            }

            self::$instance->set_charset("utf8mb4");
        }

        return self::$instance;
    }

    public static function beginTransaction() {
        self::connect()->begin_transaction();
    }

    public static function commit() {
        self::connect()->commit();
    }

    public static function rollback() {
        self::connect()->rollback();
    }
}
