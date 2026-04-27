<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'plan_actual')) {
                    $table->string('plan_actual', 20)->default('free')->after('is_active');
                }

                if (!Schema::hasColumn('users', 'is_trial')) {
                    $table->boolean('is_trial')->default(false)->after('plan_actual');
                }

                if (!Schema::hasColumn('users', 'trial_ends_at')) {
                    $table->timestamp('trial_ends_at')->nullable()->after('is_trial');
                }
            });
        }

        if (Schema::hasTable('news')) {
            Schema::table('news', function (Blueprint $table) {
                if (!Schema::hasColumn('news', 'access_level')) {
                    $table->string('access_level', 20)->default('free')->after('status');
                }

                if (!Schema::hasColumn('news', 'is_premium')) {
                    $table->boolean('is_premium')->default(false)->after('access_level');
                }
            });
        }

        if (Schema::hasTable('users') && !Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->enum('plan', ['free', 'pro', 'business'])->default('free');
                $table->enum('status', ['active', 'canceled', 'trial'])->default('active');
                $table->timestamp('start_date')->nullable();
                $table->timestamp('end_date')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'status']);
                $table->index(['plan', 'status']);
            });
        }

        if (Schema::hasTable('news') && !Schema::hasTable('insights')) {
            Schema::create('insights', function (Blueprint $table) {
                $table->id();
                $table->foreignId('noticia_id')->constrained('news')->cascadeOnDelete();
                $table->text('resumen');
                $table->text('impacto')->nullable();
                $table->unsignedTinyInteger('relevancia')->default(50);
                $table->text('insight_accionable')->nullable();
                $table->enum('tipo', ['tendencia', 'oportunidad', 'riesgo'])->default('tendencia');
                $table->boolean('is_premium')->default(true);
                $table->timestamps();

                $table->index(['noticia_id', 'is_premium']);
                $table->index(['tipo', 'relevancia']);
            });
        }

        if (Schema::hasTable('users') && !Schema::hasTable('alerts')) {
            Schema::create('alerts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('keyword');
                $table->string('categoria')->nullable();
                $table->string('frecuencia', 30)->default('weekly');
                $table->timestamps();

                $table->index(['user_id', 'keyword']);
            });
        }

        if (!Schema::hasTable('metrics_events')) {
            Schema::create('metrics_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('event_type', 80);
                $table->json('metadata')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->index(['event_type', 'created_at']);
                $table->index(['user_id', 'event_type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('metrics_events');
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('insights');
        Schema::dropIfExists('subscriptions');

        if (Schema::hasTable('news')) {
            Schema::table('news', function (Blueprint $table) {
                foreach (['access_level', 'is_premium'] as $column) {
                    if (Schema::hasColumn('news', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                foreach (['plan_actual', 'is_trial', 'trial_ends_at'] as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
