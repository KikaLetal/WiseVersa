<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TranslationService {
    private string $YandexKey;
    private array $supportedPairs = [
        'ru-en', 'en-ru', 'ru-de', 'de-ru', 'ru-fr', 'fr-ru',
        'en-de', 'de-en', 'en-fr', 'fr-en', 'de-fr', 'fr-de'
    ];

    public function __construct() {
        $this->YandexKey = config('services.yandex.dict_key') ?? env('YANDEX_DICT_KEY', '');    
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

        $url = "https://dictionary.yandex.net/api/v1/dicservice.json/lookup"; //?key={$this->YandexKey}&lang={$langPair}&text=" . urlencode($word)

        try{
            $response = Http::timeout(5)->get($url, [
                'key'  => $this->YandexKey,
                'lang' => $langPair,
                'text' => $word
            ]);

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();
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
            } catch (\Exception $e) {
                return null; 
            }
        }

    private function translateWithMyMemory(
        string $word, 
        string $sourceLang, 
        string $targetLang): ?array{

        $url = "https://api.mymemory.translated.net/get"; //?q=" . urlencode($word) . "&langpair={$sourceLang}|{$targetLang}";
        
        try{
            $response = Http::timeout(10)->get($url, [
                'q'        => $word,
                'langpair' => "{$sourceLang}|{$targetLang}"
            ]);

            if ($response->failed()) {
                return ['success' => false, 'error' => 'Translation service unavailable'];
            }

            $result = $response->json();
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
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Translation service unavailable'];
        }
    }
}