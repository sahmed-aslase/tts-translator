<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TranslationService
{
    public function translate(string $text, string $source, string $target): string
    {
        // Use MyMemory only (free, no API key). Note: rate-limited.
        return $this->viaMyMemory($text, $source, $target);
    }

    protected function viaMyMemory(string $text, string $source, string $target): string
    {
        $resp = Http::get('https://api.mymemory.translated.net/get', [
            'q' => $text,
            'langpair' => $source . '|' . $target,
        ]);
        $resp->throw();
        $json = $resp->json();
        return $json['responseData']['translatedText'] ?? '';
    }
}
