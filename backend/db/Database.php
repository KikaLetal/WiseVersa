<?php

class Database {
    private static $instance = null;
    public static function connect(){
        if (self::$instance === null) {
            self::$instance = new mysqli(
                "localhost",
                'root',
                "kita",
                "translator_db"
            );

            if(self::$instance->connect_error) {
                die("DB connection failed: " . self::$instance->connect_error);
            }

            self::$instance->set_charset("utf8mb4");
        }

        return self::$instance;
    }
}