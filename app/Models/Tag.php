<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * Get the translation tag for the translation.
     */
    public function translationTag()
    {
        return $this->hasOne(TranslationTag::class);
    }

    /**
     * Get the translations for the tag.
     */
    public function translations()
    {
        return $this->hasMany(Translation::class);
    }
}
