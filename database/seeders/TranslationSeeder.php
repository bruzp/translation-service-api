<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $limit = 200000;
        $chunkSize = 10000;

        $locales = Locale::pluck('id')->toArray();
        $tags = Tag::pluck('id')->toArray();

        for ($i = 0; $i < $limit; $i += $chunkSize) {
            $translations = [];
            $keys = [];
            $now = now();

            for ($j = 0; $j < $chunkSize; $j++) {
                $number = $i + $j;
                $key = 'test.key.' . $number;

                $keys[] = $key;

                $translations[] = [
                    'locale_id' => $locales[array_rand($locales)],
                    'key' => $key,
                    'value' => 'test value ' . $number,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            Translation::insert($translations);

            $insertedTranslations = Translation::query()
                ->whereIn('key', $keys)
                ->pluck('id')
                ->toArray();

            $translationTags = [];

            foreach ($insertedTranslations as $translationId) {
                $randomTagIds = collect($tags)
                    ->shuffle()
                    ->take(random_int(1, 3));

                foreach ($randomTagIds as $tagId) {
                    $translationTags[] = [
                        'translation_id' => $translationId,
                        'tag_id' => $tagId,
                    ];
                }
            }

            DB::table('translation_tags')->insertOrIgnore($translationTags);
        }
    }
}
