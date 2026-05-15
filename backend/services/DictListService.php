<?php
require_once BASE_PATH . '/repo/DictListRepository.php';

class DictListService {
    private DictListRepository $repository;

    public function __construct() {
        $this->repository = new DictListRepository();
    }   
    public function getLists($user_Id) {
        return $this->repository->getLists($user_Id);
    }

    public function createList($data) {
        
        if (!isset($data['name'], $data['source_lang'], $data['target_lang'])) {
            return [
                "error" => "Missing fields"
            ];
        }

        $id = $this->repository->createList($data);

        return [
            "id" => $id,
            "name" => $data['name']
        ];
    }
}