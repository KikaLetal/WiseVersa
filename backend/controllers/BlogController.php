<?php
require_once BASE_PATH . '/services/BlogService.php';
require_once BASE_PATH . '/middleware/AuthMiddleware.php';

class BlogController {
    private BlogService $service;
    
    public function __construct() {
        $this->service = new BlogService();
    }

    public function getPosts() {
        $auth = new AuthMiddleware();
        $payload = $auth->handle();
        $userId = $payload['id'];
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = min(50, (int)($_GET['limit'] ?? 20));
        
        $posts = $this->service->getFeed($userId, $page, $limit);
        
        echo json_encode([
            'success' => true,
            'data' => $posts,
            'pagination' => ['page' => $page, 'limit' => $limit]
        ]);
    }

    public function getPost($postId) {
        $auth = new AuthMiddleware();
        $payload = $auth->handle();
        $userId = $payload['id'];
        
        $post = $this->service->getPost((int)$postId, $userId);
        
        if (!$post) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Post not found']);
            return;
        }
        
        echo json_encode(['success' => true, 'data' => $post]);
    }

    public function createPost() {
        $auth = new AuthMiddleware();
        $payload = $auth->handle();
        $userId = $payload['id'];

        $userRepo = new UserRepository();
        $user = $userRepo->getUserById($userId);

        if (!in_array($user['role'], ['admin', 'moderator'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Only admins can create posts']);
            return;
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $content = null;
        $previewImage = null;

        if (strpos($contentType, 'multipart/form-data') !== false) {
            $content = $_POST['content'] ?? '';

             if (isset($_FILES['preview']) && $_FILES['preview']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = BASE_PATH . '/uploads/blog/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $extension = pathinfo($_FILES['preview']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $destination = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['preview']['tmp_name'], $destination)) {
                    $previewImage = '/uploads/blog/' . $filename;
                }
            }
        } else{
            $data = json_decode(file_get_contents('php://input'), true);
            $content = $data['content'] ?? '';
            $previewImage = $data['preview'] ?? null;
        }
        
        if (empty($content)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Content is required']);
            return;
        }
        
        $post = $this->service->createPost($userId, $content, $previewImage);
        
        if (!$post) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to create post']);
            return;
        }
        
        echo json_encode(['success' => true, 'data' => $post]);
    }

    public function updatePost($postId) {
        $auth = new AuthMiddleware();
        $payload = $auth->handle();
        $userId = $payload['id'];

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $content = null;
        $previewImage = null;

        if (strpos($contentType, 'multipart/form-data') !== false) {
            $content = $_POST['content'] ?? '';
            
            if (isset($_FILES['preview']) && $_FILES['preview']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = BASE_PATH . '/uploads/blog/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $extension = pathinfo($_FILES['preview']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $destination = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['preview']['tmp_name'], $destination)) {
                    $previewImage = '/uploads/blog/' . $filename;
                }
            }

            if (!$previewImage && isset($_POST['existing_preview'])) {
                $previewImage = $_POST['existing_preview'];
            }
        } else {
            $data = json_decode(file_get_contents('php://input'), true);
            $content = $data['content'] ?? '';
            $previewImage = $data['preview'] ?? null;
        }
        
        if (empty($content)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Content is required']);
            return;
        }
            
        $result = $this->service->updatePost((int)$postId, $userId, $content, $previewImage);
        
        if (!$result) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Post not found or no permission']);
            return;
        }
        
        echo json_encode(['success' => true]);  
    }

    public function deletePost($postId) {
        $auth = new AuthMiddleware();
        $payload = $auth->handle();
        $userId = $payload['id'];
        
        $result = $this->service->deletePost((int)$postId, $userId);
        
        if (!$result) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Post not found or no permission']);
            return;
        }
        
        echo json_encode(['success' => true]);
    }

    public function toggleLike($postId) {
        $auth = new AuthMiddleware();
        $payload = $auth->handle();
        $userId = $payload['id'];
        
        $result = $this->service->toggleLike((int)$postId, $userId);
        
        echo json_encode(['success' => true, 'data' => $result]);
    }
}