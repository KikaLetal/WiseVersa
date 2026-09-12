<?php
require_once BASE_PATH . '/db/Database.php';

class BlogRepository {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAllPosts(int $userId, int $limit = 50, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT bp.*, u.username, u.profile_picture,
                   (SELECT COUNT(*) FROM blog_likes WHERE post_id = bp.id) as likes_count,
                   (SELECT COUNT(*) FROM blog_likes WHERE post_id = bp.id AND user_id = :user_id) as user_liked
            FROM blog_posts bp
            JOIN users u ON bp.author_id = u.id
            ORDER BY bp.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getPostById(int $postId, int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT bp.*, u.username, u.profile_picture,
                   (SELECT COUNT(*) FROM blog_likes WHERE post_id = bp.id) as likes_count,
                   (SELECT COUNT(*) FROM blog_likes WHERE post_id = bp.id AND user_id = :user_id) as user_liked
            FROM blog_posts bp
            JOIN users u ON bp.author_id = u.id
            WHERE bp.id = :post_id
        ");
        $stmt->execute(['user_id' => $userId, 'post_id' => $postId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function createPost(int $authorId, string $content, ?string $previewImage = null): ?int {
        $stmt = $this->db->prepare("
            INSERT INTO blog_posts (author_id, content, preview)
            VALUES (:author_id, :content, :preview)
        ");

        $res = $stmt->execute([
            'author_id' => $authorId,
            'content' => $content,
            'preview' => $previewImage
        ]);
        return $res ? (int)$this->db->lastInsertId() : null;
    }

    public function updatePost(int $postId, int $authorId, string $content, ?string $previewImage = null): bool {
        if ($previewImage !== null) {
            $stmt = $this->db->prepare("
                UPDATE blog_posts
                SET content = :content, preview = :preview, updated_at = CURRENT_TIMESTAMP
                WHERE id = :post_id AND author_id = :author_id
            ");
            $stmt->execute([
                'content' => $content,
                'preview' => $previewImage,
                'post_id' => $postId,
                'author_id' => $authorId
            ]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE blog_posts
                SET content = :content, updated_at = CURRENT_TIMESTAMP
                WHERE id = :post_id AND author_id = :author_id
            ");
            $stmt->execute([
                'content' => $content,
                'post_id' => $postId,
                'author_id' => $authorId
            ]);
        }
        return $stmt->rowCount() > 0;
    }

    public function deletePost(int $postId, int $authorId): bool {
        $stmt = $this->db->prepare("DELETE FROM blog_posts WHERE id = :post_id AND author_id = :author_id");
        $stmt->execute([
            'post_id' => $postId,
            'author_id' => $authorId
        ]);
        return $stmt->rowCount() > 0;
    }

    public function likePost(int $postId, int $userId): bool {
        $stmt = $this->db->prepare("INSERT IGNORE INTO blog_likes (user_id, post_id) VALUES (:user_id, :post_id)");
        $stmt->execute([
            'user_id' => $userId,
            'post_id' => $postId
        ]);
        return $stmt->rowCount() > 0;
    }
    
    public function unlikePost(int $postId, int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM blog_likes WHERE user_id = :user_id AND post_id = :post_id");
        $stmt->execute([
            'user_id' => $userId,
            'post_id' => $postId
        ]);
        return $stmt->rowCount() > 0;
    }
    
    public function getUserLike(int $postId, int $userId): bool {
        $stmt = $this->db->prepare("SELECT 1 FROM blog_likes WHERE user_id = :user_id AND post_id = :post_id");
        $stmt->execute([
            'user_id' => $userId,
            'post_id' => $postId
        ]);
        return (bool)$stmt->fetch();
    }
    
    public function getLikesCount(int $postId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM blog_likes WHERE post_id = :post_id");
        $stmt->execute(['post_id' => $postId]);
        return (int)($stmt->fetch()['count'] ?? 0);
    }

}