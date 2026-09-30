<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\User;

class BlogService
{
    public function getAllPosts(int $userId, int $limit = 50, int $offset = 0) {
        return BlogPost::query()
            ->with('author:id,username,profile_picture') 
            ->withCount('likedByUsers')
            ->withExists(['likedByUsers as user_liked' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->orderByDesc('created_at')
            ->offset($offset)
            ->limit($limit)
            ->get();
    }

    public function findPostById(int $postId, int $userId): ?BlogPost
    {
        return BlogPost::query()
            ->with('author:id,username,profile_picture')
            ->withCount('likedByUsers')
            ->withExists(['likedByUsers as user_liked' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->find($postId);
    }

    public function createPost(int $authorId, string $content, ?string $previewImage = null): BlogPost
    {
        return BlogPost::create([
            'author_id' => $authorId,
            'content'   => $content,
            'preview'   => $previewImage,
        ]);
    }

    public function updatePost(int $postId, int $authorId, array $data): bool
    {
        $post = BlogPost::where('id', $postId)->where('author_id', $authorId)->first();

        if (!$post) {
            return false;
        }

        return $post->update($data);
    }

    public function deletePost(int $postId, int $authorId): bool {
        $post = BlogPost::where('id', $postId)->where('author_id', $authorId)->first();

        if (!$post) {
            return false;
        }

        return $post->delete();
    }

    public function toggleLike(int $postId, int $userId): array {
        $user = User::find($userId);

        $res = $user->likedPosts()->toggle($postId);

        return [
            'attached' => count($res['attached']) > 0,
            'likes_count' => BlogPost::find($postId)->likedByUsers()->count()
        ];
    }
}