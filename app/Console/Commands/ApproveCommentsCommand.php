<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Services\CommentValidationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ApproveCommentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comments:auto-approve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically approve pending comments that meet the content standards';

    /**
     * Execute the console command.
     *
     * @param CommentValidationService $validator
     * @return int
     */
    public function handle(CommentValidationService $validator)
    {
        $pendingComments = Comment::pending()->get();
        
        if ($pendingComments->isEmpty()) {
            $this->info('No pending comments to process.');
            return 0;
        }

        $this->info('Processing ' . $pendingComments->count() . ' pending comments...');
        
        $approved = 0;
        $rejected = 0;
        
        foreach ($pendingComments as $comment) {
            $validationResult = $validator->validate($comment->content);
            
            if ($validationResult['isValid']) {
                $comment->status = 'approved';
                $comment->save();
                $approved++;
                
                $this->line("Comment #{$comment->id} approved automatically.");
            } else {
                // Verificamos si está habilitado el auto-rechazo en la configuración
                if (config('comments.auto_reject', false)) {
                    $comment->status = 'rejected';
                    $comment->save();
                    $rejected++;
                    
                    $this->error("Comment #{$comment->id} rejected automatically. Reason: {$validationResult['reason']}");
                } else {
                    // En caso contrario, lo dejamos pendiente para revisión manual
                    $this->warn("Comment #{$comment->id} requires manual review. Reason: {$validationResult['reason']}");
                }
            }
        }
        
        $this->info("Processed {$pendingComments->count()} comments. Approved: {$approved}, Left for review: " . ($pendingComments->count() - $approved));
        
        // Registrar la actividad en el log
        Log::info("Auto-approve comments task completed. Processed: {$pendingComments->count()}, Approved: {$approved}, Rejected: {$rejected}");
        
        return 0;
    }
}