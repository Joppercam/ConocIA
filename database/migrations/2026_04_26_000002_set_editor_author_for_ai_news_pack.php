<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensureAuthorIdColumn();

        $editorId = $this->editorId();

        $payload = [
            'author' => 'Editor',
            'updated_at' => now(),
        ];

        if ($editorId !== null) {
            if (Schema::hasColumn('news', 'author_id')) {
                $payload['author_id'] = $editorId;
            }

            if (Schema::hasColumn('news', 'user_id')) {
                $payload['user_id'] = $editorId;
            }
        }

        DB::table('news')
            ->whereIn('slug', $this->slugs())
            ->update($payload);

        $this->clearNewsCache();
    }

    public function down(): void
    {
        DB::table('news')
            ->whereIn('slug', $this->slugs())
            ->update([
                'author' => 'Equipo ConocIA',
                'updated_at' => now(),
            ]);

        if (Schema::hasColumn('news', 'author_id')) {
            DB::table('news')
                ->whereIn('slug', $this->slugs())
                ->update(['author_id' => null]);
        }

        if (Schema::hasColumn('news', 'user_id')) {
            DB::table('news')
                ->whereIn('slug', $this->slugs())
                ->update(['user_id' => null]);
        }

        $this->clearNewsCache();
    }

    private function ensureAuthorIdColumn(): void
    {
        if (Schema::hasColumn('news', 'author_id')) {
            return;
        }

        Schema::table('news', function ($table) {
            $table->unsignedBigInteger('author_id')->nullable()->after('user_id');
            $table->index('author_id', 'news_author_id_index');
        });
    }

    private function editorId(): ?int
    {
        $roleIds = DB::table('roles')
            ->whereIn('slug', ['editor', 'admin'])
            ->pluck('id');

        $editorId = DB::table('users')
            ->whereIn('role_id', $roleIds)
            ->where(function ($query) {
                $query->where('email', 'editor@conocia.com')
                    ->orWhere('username', 'editor')
                    ->orWhere('name', 'Editor');
            })
            ->orderByRaw("CASE WHEN email = 'editor@conocia.com' THEN 0 ELSE 1 END")
            ->value('id');

        if ($editorId) {
            return (int) $editorId;
        }

        $fallbackId = DB::table('users')
            ->whereIn('role_id', $roleIds)
            ->orderBy('id')
            ->value('id');

        return $fallbackId ? (int) $fallbackId : null;
    }

    private function slugs(): array
    {
        return [
            'deepseek-lanza-v4-competencia-china-estados-unidos-ia',
            'google-inversion-40000-millones-anthropic',
            'google-cloud-next-2026-agentes-empresariales-tpu',
            'openai-lanza-gpt-5-5-agentes-mas-capaces',
            'google-gemini-proxima-generacion-siri-apple-intelligence',
        ];
    }

    private function clearNewsCache(): void
    {
        foreach ([
            'home_page_data',
            'home_page_data_v2',
            'all_published_news',
            'all_published_news_v2',
            'popular_news',
            'secondary_news',
            'trending_ids',
            'news_index_list',
            'most_read_articles',
            'popular_tags',
            'all_categories',
        ] as $key) {
            Cache::forget($key);
        }
    }
};
