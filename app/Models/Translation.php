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

    /**
     * Scopes
     */
    public function scopeLocale($query, $locale)
    {
        return $query->where('locale', $locale);
    }

    public function scopeKeys($query, $keys)
    {
        return $query->whereIn('key', $keys);
    }

    public function scopeTags($query, $tags)
    {
        return $query->whereHas('translationTag', function ($query) use ($tags) {
            $query->whereIn('tag_id', $tags);
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('key', 'like', "%$search%")
            ->orWhere('content', 'like', "%$search%");
    }
}
