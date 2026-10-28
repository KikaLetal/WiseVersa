<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\BlogPost;
use App\Services\BlogService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(
        private BlogService $blogService
    ) {}

    public function getPosts(Request $request)
    {
        $validated = $request->validate([
            'page' => ['sometimes', 'integer', 'min:1'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        $page = (int) ($validated['page'] ?? 1);
        $limit = (int) ($validated['limit'] ?? 20);
        $offset = ($page - 1) * $limit;

        $posts = $this->blogService->getAllPosts(
            (int) $request->user()->id,
            $limit,
            $offset
        );

        return response()->json([
            'success' => true,
            'data' => $posts,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
            ],
        ]);
    }

    public function getPost(Request $request, int $postId)
    {
        $post = $this->blogService->findPostById(
            $postId,
            (int) $request->user()->id
        );

        if (!$post) {
            return response()->json([
                'success' => false,
                'error' => 'Post not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $post,
        ]);
    }

    public function createPost(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'moderator'], true)) {
            return response()->json([
                'success' => false,
                'error' => 'Only admins and moderators can create posts',
            ], 403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string'],
            'preview' => ['nullable', 'image', 'max:5120'],
        ]);

        $previewPath = null;

        if ($request->hasFile('preview')) {
            $path = $request->file('preview')->store('blog', 'public');
            $previewPath = asset('storage/' . $path);
        }

        $post = $this->blogService->createPost(
            (int) $user->id,
            $validated['content'],
            $previewPath
        );

        $post->load('author:id,username,profile_picture');
        $post->loadCount('likedByUsers');
        $post->user_liked = false;

        return response()->json([
            'success' => true,
            'data' => $post,
        ], 201);
    }

    public function updatePost(Request $request, int $postId)
    {
        $validated = $request->validate([
            'content' => ['required', 'string'],
            'preview' => ['sometimes', 'nullable', 'image', 'max:5120'],
            'existing_preview' => ['sometimes', 'nullable', 'string', 'max:2048'],
        ]);

        $data = [
            'content' => $validated['content'],
        ];

        if ($request->hasFile('preview')) {
            $path = $request->file('preview')->store('blog', 'public');
            $data['preview'] = asset('storage/' . $path);
        } elseif (array_key_exists('existing_preview', $validated)) {
            $data['preview'] = $validated['existing_preview'];
        }

        $updated = $this->blogService->updatePost(
            $postId,
            (int) $request->user()->id,
            $data
        );

        if (!$updated) {
            return response()->json([
                'success' => false,
                'error' => 'Post not found or no permission',
            ], 404);
        }

        return response()->json(['success' => true]);
    }

    public function deletePost(Request $request, int $postId)
    {
        $deleted = $this->blogService->deletePost(
            $postId,
            (int) $request->user()->id
        );

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'error' => 'Post not found or no permission',
            ], 404);
        }

        return response()->json(['success' => true]);
    }

    public function toggleLike(Request $request, int $postId)
    {
        $post = BlogPost::find($postId);

        if (!$post) {
            return response()->json([
                'success' => false,
                'error' => 'Post not found',
            ], 404);
        }

        $result = $this->blogService->toggleLike(
            $postId,
            (int) $request->user()->id
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
