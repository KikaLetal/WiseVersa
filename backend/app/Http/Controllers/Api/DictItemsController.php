<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Api\Controller;
use App\Models\DictLists;
use App\Services\DictionariesService;
use Illuminate\Http\Request;

class DictItemsController extends Controller
{
    public function __construct(
        private DictionariesService $dictionariesService
    ) {}

    public function getItems(Request $request, int $listId)
    {
        $list = $this->dictionariesService->getListById($listId);

        if (!$list || (int) $list->user_id !== (int) $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => 'Access denied',
            ], 403);
        }

        $items = $this->dictionariesService->getItems($listId);

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'list_id' => ['required', 'integer', 'min:1'],
            'word' => ['required', 'string', 'max:255'],
            'translation' => ['required', 'string', 'max:255'],
            'allow_duplicate' => ['sometimes', 'boolean'],
        ]);

        $userId = (int) $request->user()->id;
        $listId = (int) $validated['list_id'];

        $list = $this->dictionariesService->getListById($listId);

        if (!$list || (int) $list->user_id !== $userId) {
            return response()->json([
                'success' => false,
                'error' => 'Access denied',
            ], 403);
        }

        $item = $this->dictionariesService->addItem(
            $listId,
            $validated['word'],
            $validated['translation'],
            $validated['allow_duplicate'] ?? false
        );

        if (!$item) {
            return response()->json([
                'success' => false,
                'error' => 'This word and translation already exist in the list',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'id' => $item->id,
            'data' => $item,
        ], 201);
    }

    public function deleteItem(Request $request, int $listId, int $itemId)
    {
        $deleted = $this->dictionariesService->deleteItem(
            (int) $request->user()->id,
            $listId,
            $itemId
        );

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete item',
            ], 404);
        }

        return response()->json(['success' => true]);
    }

    public function updateMastered(Request $request, int $itemId)
    {
        $validated = $request->validate([
            'mastered' => ['required', 'boolean'],
        ]);

        $updated = $this->dictionariesService->updateMastered(
            $itemId,
            $validated['mastered'],
            (int) $request->user()->id
        );

        if (!$updated) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update item',
            ], 404);
        }

        return response()->json(['success' => true]);
    }

    public function resetProgress(Request $request, int $listId)
    {
        $list = $this->dictionariesService->getListById($listId);

        if (!$list || (int) $list->user_id !== (int) $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => 'Access denied',
            ], 403);
        }

        $this->dictionariesService->resetMasteredForList(
            $listId,
            (int) $request->user()->id
        );

        return response()->json(['success' => true]);
    }
}
