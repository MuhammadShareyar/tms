<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = ['key', 'content', 'locale'];

    /**
     * Get the translation tag for the translation.
     */
    public function translationTag()
    {
        return $this->hasOne(TranslationTag::class);
    }
}
