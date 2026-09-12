<?php

require_once __DIR__ . '/db/Database.php';

class AdminCreator {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::connect();
    }

    private function generateRandomPassword(int $length = 12): string {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        return substr(str_shuffle($chars), 0, $length);
    }

    private function generateRandomUsername(string $base = 'admin'): string {
        return $base . '_' . rand(1000, 9999);
    }

    private function usernameExists(string $username): bool {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            return (bool)$stmt->fetch();;
    }
        
    public function createAdmin(?string $username = null): array {
        if (!$username) {
            $username = $this->generateRandomUsername();
        }
        
        if ($this->usernameExists($username)) {
            return [
                'success' => false,
                'message' => "Username '{$username}' already exists"
            ];
        }

        $originalUsername = $username;
        $counter = 1;
        while ($this->usernameExists($username)) {
            $username = $originalUsername . '_' . $counter;
            $counter++;
            if ($counter > 100) {
                return [
                    'success' => false,
                    'message' => "Cannot generate unique username after 100 attempts"
                ];
            }
        }
        
        $password = $this->generateRandomPassword();
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("
            INSERT INTO users (username, password_hash, role, profile_picture)
            VALUES (:username, :password_hash, 'admin', 'default.png')
        ");

        if ($stmt->execute(['username' => $username, 'password_hash' => $passwordHash])) {
            return [
                'success' => true,
                'username' => $username,
                'password' => $password,
                'user_id' => $this->db->lastInsertId(),
                'message' => "Admin created successfully"
            ];
        }
        
        return [
            'success' => false,
            'message' => "Failed to create admin"
        ];
    }
}

function main() {
    $creator = new AdminCreator();

    $options = getopt('', ['username::', 'help']);

    if (isset($options['help'])) {
        echo "Usage: php create_admin.php [--username=NAME] [--count=NUM]\n";
        echo "  --username  : Base username (optional, auto-generated if not provided)\n";
        echo "  --help      : Show this help\n";
        exit(0);
    }
    
    $baseUsername = $options['username'] ?? null;

    echo "\n=== Creating admin ===\n\n";
    
    $result = $creator->createAdmin($baseUsername);
    
    if ($result['success']) {
        echo "✅ " . $result['message'] . "\n";
        echo "   Username: " . $result['username'] . "\n";
        echo "   Password: " . $result['password'] . "\n";
        echo "   User ID: " . $result['user_id'] . "\n";
    } else {
        echo "❌ Error: " . $result['message'] . "\n";
    }
    
    echo "\n";
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    main();
}