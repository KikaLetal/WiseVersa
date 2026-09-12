<?php

require_once BASE_PATH . '/repo/UserRepository.php';
require_once BASE_PATH . '/repo/DictListRepository.php';
require_once BASE_PATH .'/services/JWTService.php';

class TranslationService {
    private string $YandexKey;
    private array $supportedPairs = [
        'ru-en', 'en-ru',
        'ru-de', 'de-ru',
        'ru-fr', 'fr-ru',
        'en-de', 'de-en',
        'en-fr', 'fr-en',
        'de-fr', 'fr-de'
    ];

    public function __construct() {
        $config = require BASE_PATH . '/config.php';
        $this->YandexKey = $config['yandex_dict_key'] ?? '';    
    }

    public function translate(string $word, string $sourceLang, string $targetLang): array{
        $sourceLang = strtolower($sourceLang);
        $targetLang = strtolower($targetLang);
        $langPair = "{$sourceLang}-{$targetLang}";
        
        if($this->YandexKey && in_array($langPair, $this->supportedPairs)){
            $result = $this->translateWithYandex($word, $sourceLang, $targetLang);
            if ($result !== null) {
                return $result;
            }
        }

        return $this->translateWithMyMemory($word, $sourceLang, $targetLang);
    }

    private function translateWithYandex(
        string $word, 
        string $sourceLang, 
        string $targetLang): ?array{

        $langPair = "{$sourceLang}-{$targetLang}";

        $url = "https://dictionary.yandex.net/api/v1/dicservice.json/lookup?key={$this->YandexKey}&lang={$langPair}&text=" . urlencode($word);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            return null;
        }

        $data = json_decode($response, true);
        if (empty($data['def'])) {
            return null;
        }

        $def = $data['def'][0];
        $transcription = $def['ts'] ?? null;
        $translations = [];

        foreach ($def['tr'] as $tr) {
            $item = [
                'text' => $tr['text'],
                'pos' => $tr['pos'] ?? null,
                'gender' => $tr['gen'] ?? null, 
                'examples' => []
            ];

            if (isset($tr['ex'])) {
                foreach ($tr['ex'] as $ex) {
                    $item['examples'][] = [
                        'source' => $ex['text'],
                        'target' => $ex['tr'][0]['text'] ?? null
                    ];
                }
            }
            $translations[] = $item;
        }

        return [
            'success' => true,
            'source' => 'yandex',
            'word' => $word,
            'transcription' => $transcription,
            'translations' => $translations
        ];
    }

    private function translateWithMyMemory(
        string $word, 
        string $sourceLang, 
        string $targetLang): ?array{

        $url = "https://api.mymemory.translated.net/get?q=" . urlencode($word) . "&langpair={$sourceLang}|{$targetLang}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            return ['success' => false, 'error' => 'Translation service unavailable'];
        }

        $result = json_decode($response, true);
        $translation = $result['responseData']['translatedText'] ?? null;

        if (!$translation || $translation === $word) {
            return ['success' => false, 'error' => 'No translation found'];
        }

        return [
            'success' => true,
            'source' => 'mymemory',
            'word' => $word,
            'translation' => $translation
        ];
    }
}