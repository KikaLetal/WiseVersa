<?php
require_once BASE_PATH . '/repo/BlogRepository.php';

class BlogService {
    private BlogRepository $repo;
    
    public function __construct() {
        $this->repo = new BlogRepository();
    }

    public function getFeed(int $userId, int $page = 1, int $limit = 20): array {
        $offset = ($page - 1) * $limit;
        return $this->repo->getAllPosts($userId, $limit, $offset);
    }

    public function getPost(int $postId, int $userId): ?array {
        return $this->repo->getPostById($postId, $userId);
    }
    
    public function createPost(int $authorId, string $content, ?string $previewImage = null): ?array {
        $postId = $this->repo->createPost($authorId, $content, $previewImage);
        if (!$postId) return null;
        return $this->getPost($postId, $authorId);
    }
    
    public function updatePost(int $postId, int $authorId, string $content, ?string $previewImage = null): bool {
        return $this->repo->updatePost($postId, $authorId, $content, $previewImage);
    }
    
    public function deletePost(int $postId, int $authorId): bool {
        return $this->repo->deletePost($postId, $authorId);
    }
    
    public function toggleLike(int $postId, int $userId): array {
        $userLiked = $this->repo->getUserLike($postId, $userId);
        
        if ($userLiked) {
            $this->repo->unlikePost($postId, $userId);
            $liked = false;
        } else {
            $this->repo->likePost($postId, $userId);
            $liked = true;
        }
        
        $likesCount = $this->repo->getLikesCount($postId);
        
        return [
            'liked' => $liked,
            'likes_count' => $likesCount
        ];
    }
}