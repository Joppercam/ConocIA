<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VideoPlatform;

class VideoPlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = [
            [
                'name' => 'YouTube',
                'code' => 'youtube',
                'embed_pattern' => 'https://www.youtube.com/embed/{id}',
                'is_active' => true,
            ],
            [
                'name' => 'Vimeo',
                'code' => 'vimeo',
                'embed_pattern' => 'https://player.vimeo.com/video/{id}',
                'is_active' => true,
            ],
            [
                'name' => 'Dailymotion',
                'code' => 'dailymotion',
                'embed_pattern' => 'https://www.dailymotion.com/embed/video/{id}',
                'is_active' => true,
            ],
        ];

        foreach ($platforms as $platform) {
            VideoPlatform::updateOrCreate(
                ['code' => $platform['code']],
                $platform
            );
        }
    }
}
