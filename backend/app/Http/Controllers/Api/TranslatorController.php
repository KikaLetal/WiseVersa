<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Services\TranslationService;

class TranslatorController extends Controller
{
    public function __construct(
        private TranslationService $translationService
    ) {}

    public function translate(Request $request)
    {

        $validatedData = $request->validate([
            'word' => ['required', 'string', 'max:1000'],
            'sourceLang' => 'nullable|string|in:ru,en,de,fr',
            'targetLang' => 'nullable|string|in:ru,en,de,fr',
        ]);   

        $word = trim($validatedData['word']);

        if ($word === '') {
            return response()->json([
                'success' => false,
                'error' => 'Word is required',
            ], 422);
        }

        $result = $this->translationService->translate(
            $word, 
            $validatedData['sourceLang'] ?? 'ru',
            $validatedData['targetLang'] ?? 'en'
        );

        $status = ($result['success'] ?? false) ? 200 : 500;

        return response()->json($result, $status);
    }
}
