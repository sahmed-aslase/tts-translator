<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TranslationController extends Controller
{
    public function index()
    {
        $history = Translation::latest()->paginate(10);
        $languages = [
            ['code' => 'es', 'name' => 'Spanish'],
            ['code' => 'fr', 'name' => 'French'],
            ['code' => 'de', 'name' => 'German'],
        ];

        return view('translator', compact('history', 'languages'));
    }

    public function translate(Request $request, TranslationService $service)
    {
        $validated = $request->validate([
            'text' => 'required|string|min:1',
            'targets' => 'required|array|min:1',
            'targets.*' => 'string|in:es,fr,de',
        ]);

        $text = $validated['text'];
        $source = 'en';
        $allowed = ['es','fr','de'];
        $targets = array_values(array_unique(array_intersect($allowed, $validated['targets'])));
        if (empty($targets)) {
            return response()->json(['message' => 'Please choose at least one target language.'], 422);
        }

        $results = [];
        foreach ($targets as $target) {
            try {
                $translated = $service->translate($text, $source, $target);
            } catch (\Throwable $e) {
                Log::error('Translation failed', ['error' => $e->getMessage()]);
                return response()->json([
                    'message' => 'Translation failed for '.$target.'. Please try again later.'
                ], 502);
            }

            $record = Translation::create([
                'source_text' => $text,
                'source_lang' => $source,
                'target_lang' => $target,
                'translated_text' => $translated,
            ]);

            $results[] = [
                'id' => $record->id,
                'target' => $target,
                'translated' => $translated,
            ];
        }

        return response()->json(['items' => $results]);
    }

}
