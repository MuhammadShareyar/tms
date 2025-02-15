<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('translation_tags')->truncate();
        DB::table('translations')->truncate();

        $locales = ['en', 'fr', 'es', 'de'];
        $translations = [];

        $tagCount = Tag::count();

        $transTagKey = 1;

        for ($keyId = 1; $keyId <= 10000; $keyId++) {
            $key = "key_$keyId";

            foreach ($locales as $locale) {

                $tagId = rand(1, $tagCount);

                $translations[] = [
                    'key' => $key,
                    'locale' => $locale,
                    'content' => fake()->sentence(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $translationTags[] = [
                    'translation_id' => $transTagKey,
                    'tag_id' => $tagId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $transTagKey++;
            }
        }

        foreach (array_chunk($translations, 500) as $chunk) {
            DB::table('translations')->insert($chunk);
        }

        foreach (array_chunk($translationTags, 500) as $chunk) {
            DB::table('translation_tags')->insert($chunk);
        }
    }
}
