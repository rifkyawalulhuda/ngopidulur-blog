<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('session_id', 255)->nullable()->index();
            $table->string('visitor_key', 64)->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->date('viewed_on')->index();
            $table->timestamp('viewed_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['post_id', 'visitor_key', 'viewed_on'], 'post_views_unique_daily_view');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_views');
    }
};
