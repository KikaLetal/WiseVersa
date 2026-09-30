<?php
require_once BASE_PATH . '/services/TranslationService.php';

class TranslatorController {

    private TranslationService $translationService;

    public function __construct() {
        $this->translationService = new TranslationService();
    }

    public function translate(){
        $data = json_decode(file_get_contents('php://input'), true);
        $word = $data['word'] ?? '';
        $source = $data['sourceLang'] ?? 'ru';
        $target = $data['targetLang'] ?? 'en';

        if (empty($word)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'No word provided']);
            return;
        }

        $result = $this->translationService->translate($word, $source, $target);

        if (isset($result['error'])) {
            http_response_code(500);
            echo json_encode($result);
            return;
        }

        echo json_encode($result);
    }
}