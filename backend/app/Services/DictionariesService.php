<?php

namespace App\Services;

use App\Models\DictItems;
use App\Models\DictLists;
use App\Models\User;

class DictionariesService
{
    private array $emojis = [
        "🐾", "🌟", "💡", "🎯", "🍀", "🐉", "🦄", "🎈", 
        "🔮", "🧩", "⚡", "🔥", "💎", "🌙", "☄️", "🕯️", 
        "🧸", "🎨", "🏆", "🍕", "🐧", "🦥", "🌀","🌸",  
        '😀', '😂', '😍', '🐶', '🐱', '🍎', '📚', '⭐', 
        '🔥', '❤️', '🎉', '✅', '💡', '🔤', '📖', '✏️',
        '🌍', '🎓', '🏆', '🎨', '💎', '🚀', '🌟', '🎯'
    ];

    private function getRandomEmoji(): string {
        return $this->emojis[array_rand($this->emojis)];
    }

    public function findLists(int $userId) {
        return DictLists::query()
            ->where('user_id', $userId)
            ->withCount('items')
            ->get();
    }

    public function getListById(int $listId): ?DictLists
    {
        return DictLists::find($listId);
    }

    public function createList(array $data, $iconFile = null): ?DictLists{
        $iconPath = $this->getRandomEmoji();

        if($iconFile){
            $path = $iconFile->store('list_icons', 'public');
            $iconPath = basename($path);
        }
        elseif(!empty($data['icon']) && is_string($data['icon'])) {
            $iconPath = $data['icon'];
        }
        
        return DictLists::create([
            'user_id'     => $data['user_id'],
            'name'        => $data['name'],
            'source_lang' => $data['source_lang'] ?? null,
            'target_lang' => $data['target_lang'] ?? null,
            'type'        => $data['type'] ?? 'custom',
            'icon'        => $iconPath,
        ]);
    }

    public function updateIcon(int $listId, $iconFile): bool {
        $list = DictLists::find($listId);
        if(!$list || !$iconFile){
            return false;
        }

        $path = $iconFile->store('list_icons', 'public');

        return $list->update(['icon' => basename($path)]);
    }

    public function getItems(int $listId)
    {
        return DictItems::query()
            ->where('list_id', $listId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function addItem(int $listId, string $word, string $translation): ?DictItems
    {
        $exists = DictItems::where('list_id', $listId)
            ->where('word', $word)
            ->where('translation', $translation)
            ->exists();

        if ($exists) {
            return null;
        }

        return DictItems::create([
            'list_id'     => $listId,
            'word'        => $word,
            'translation' => $translation
        ]);
    }

    public function deleteItem(int $userId, int $listId, int $itemId): bool
    {
        $item = DictItems::query()
            ->where('id', $itemId)
            ->where('list_id', $listId)
            ->whereHas('list', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();

        return $item ? (bool)$item->delete() : false;
    }

    public function updateMastered(int $itemId, bool $mastered, int $userId): bool
    {
        $item = DictItems::query()
            ->where('id', $itemId)
            ->whereHas('list', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();

        return $item ? $item->update(['mastered' => $mastered]) : false;
    }

    public function resetMasteredForList(int $listId, int $userId): bool
    {
        $updatedRows = DictItems::query()
            ->where('list_id', $listId)
            ->whereHas('list', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->update(['mastered' => false]); 

        return $updatedRows > 0;
    }
}