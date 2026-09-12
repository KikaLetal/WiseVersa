<?php
require_once BASE_PATH . '/repo/DictListRepository.php';

class DictListService {
    private DictListRepository $repository;

    private array $emojis = [
        "🐾", "🌟", "💡", "🎯", "🍀", "🐉", "🦄", "🎈", 
        "🔮", "🧩", "⚡", "🔥", "💎", "🌙", "☄️", "🕯️", 
        "🧸", "🎨", "🏆", "🍕", "🐧", "🦥", "🌀","🌸",  
        '😀', '😂', '😍', '🐶', '🐱', '🍎', '📚', '⭐', 
        '🔥', '❤️', '🎉', '✅', '💡', '🔤', '📖', '✏️',
        '🌍', '🎓', '🏆', '🎨', '💎', '🚀', '🌟', '🎯'
    ];


    public function __construct() {
        $this->repository = new DictListRepository();
    }   

    private function getRandomEmoji(): string {
        return $this->emojis[array_rand($this->emojis)];
    }

    public function getLists($user_Id) {
        $lists = $this->repository->getLists($user_Id);

        foreach ($lists as &$list) {
            if (empty($list['icon'])) {
                $list['icon'] = 'default_list.svg';
            }
            
            $list['itemsCounts'] = $this->repository->getItemsCount($list['id']);
        }
        unset($list);

        return $lists;
    }

    public function createList($data) {
        if (!isset($data['name'], $data['source_lang'], $data['target_lang'])) {
            return [
                "error" => "Missing fields"
            ];
        }

        if (isset($data['icon']) && is_array($data['icon']) && $data['icon']['tmp_name']) {
            $iconPath = $this->uploadIcon($data['icon']);
            if ($iconPath) {
                $data['icon'] = $iconPath;
            }
            else{
                $data['icon'] = $this->getRandomEmoji();
            }
        } else {
            $data['icon'] = $this->getRandomEmoji();
        }

        $id = $this->repository->createList($data);

        return [
            "id" => $id,
            "name" => $data['name'],
            "icon"    => $data['icon'],
            "type"    => 'custom',
            "source_lang" => $data['source_lang'],
            "target_lang" => $data['target_lang']
        ];
    }

    public function updateListIcon($listId, $iconFile) {
        $iconPath = $this->uploadIcon($iconFile);
        
        if (!$iconPath) {
            return ["error" => "Failed to upload icon"];
        }

        $result = $this->repository->updateIcon($listId, $iconPath);
        
        return ["success" => $result];
    }

    private function uploadIcon($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'list_icon_' . uniqid() . '.' . $extension;
        $uploadPath = BASE_PATH . "/uploads/list_icons/" . $fileName;

        if (!is_dir(dirname($uploadPath))) {
            mkdir(dirname($uploadPath), 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $fileName;
        }

        return false;
    }
}