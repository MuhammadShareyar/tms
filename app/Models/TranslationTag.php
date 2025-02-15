<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationTag extends Model
{
    protected $fillable = ['tag_id', 'translation_id'];

    /**
     * Get the translation that owns the translation tag.
     */
    public function translation()
    {
        return $this->belongsTo(Translation::class);
    }

    /**
     * Get the tag that owns the translation tag.
     */
    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
