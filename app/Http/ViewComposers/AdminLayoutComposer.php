<?php

namespace App\Http\ViewComposers;

use App\Models\Comment;
use App\Models\EditorialAgentTask;
use App\Models\GuestPost;
use App\Models\SocialMediaQueue;
use App\Models\TikTokScript;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Throwable;

class AdminLayoutComposer
{
    public const CACHE_KEY = 'admin_layout_metrics';

    public function compose(View $view): void
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return;
        }

        $data = Cache::remember(self::CACHE_KEY, 300, function () {
            try {
                $hasEditorialAgentTable = Schema::hasTable('editorial_agent_tasks');

                return [
                    'pendingSocialCount' => SocialMediaQueue::where('status', 'pending')->count(),
                    'pendingSocialPosts' => SocialMediaQueue::where('status', 'pending')
                        ->with('news')
                        ->latest()
                        ->take(3)
                        ->get(),
                    'pendingTikTokScriptsCount' => TikTokScript::where('status', 'pending_review')->count(),
                    'pendingEditorialAgentCount' => $hasEditorialAgentTable
                        ? EditorialAgentTask::where('status', 'pending')->count()
                        : 0,
                    'pendingEditorialAgentTasks' => $hasEditorialAgentTable
                        ? EditorialAgentTask::where('status', 'pending')->latest()->take(3)->get()
                        : collect(),
                    'pendingGuestPostCount' => GuestPost::pending()->count(),
                    'pendingCommentsCount' => Comment::pending()->count(),
                ];
            } catch (Throwable $e) {
                return [
                    'pendingSocialCount' => 0,
                    'pendingSocialPosts' => collect(),
                    'pendingTikTokScriptsCount' => 0,
                    'pendingEditorialAgentCount' => 0,
                    'pendingEditorialAgentTasks' => collect(),
                    'pendingGuestPostCount' => 0,
                    'pendingCommentsCount' => 0,
                ];
            }
        });

        $view->with($data);
    }
}
