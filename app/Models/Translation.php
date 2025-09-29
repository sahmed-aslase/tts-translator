<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_text',
        'source_lang',
        'target_lang',
        'translated_text',
        'voice_name',
        'speech_rate',
        'speech_pitch',
        'audio_url',
    ];
}
