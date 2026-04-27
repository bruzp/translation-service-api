<?php

namespace Database\Seeders;

use App\Models\Locale;
use Illuminate\Database\Seeder;

class LocaleSeeder extends Seeder
{
    public function run(): void
    {
        $locales = [
            'en' => 'English',
            'fr' => 'French',
            'es' => 'Spanish',
        ];

        foreach ($locales as $code => $name) {
            Locale::firstOrCreate([
                'code' => $code,
                'name' => $name,
            ]);
        }
    }
}
