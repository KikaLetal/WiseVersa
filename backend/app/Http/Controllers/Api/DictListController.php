<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Services\DictionariesService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DictListController extends Controller
{
    public function __construct(
        private DictionariesService $dictionariesService
    ) {}

    public function getLists(Request $request)
    {
        $lists = $this->dictionariesService->findLists(
            $request->user()->id
        );

        return response()->json([
            'success' => true,
            'data' => $lists,
        ]);
    }

    public function createList(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'source_lang' => ['nullable', 'string', 'max:5'],
            'target_lang' => ['nullable', 'string', 'max:5'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        $list = $this->dictionariesService->createList([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'source_lang' => $validated['source_lang'] ?? null,
            'target_lang' => $validated['target_lang'] ?? null,
            'type' => 'custom',
            'icon' => $validated['icon'] ?? null,
        ]);

        if (!$list) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create list',
            ], 500);
        }

        $list->loadCount('items');

        return response()->json([
            'success' => true,
            'data' => $list,
        ], 201);
    }

    public function updateIcon(Request $request, int $listId)
    {
        $request->validate([
            'icon' => ['required', 'image', 'max:5120'],
        ]);

        $userId = $request->user()->id;
        $list = $this->dictionariesService->getListById($listId);

        if (!$list) {
            return response()->json([
                'success' => false,
                'error' => 'List not found',
            ], 404);
        }

        if ($list->user_id !== $userId) {
            return response()->json([
                'success' => false,
                'error' => 'Access denied',
            ], 403);
        }

        $updated = $this->dictionariesService->updateIcon(
            $listId,
            $request->file('icon')
        );

        if (!$updated) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update icon',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Icon updated successfully',
        ]);
    }
}
