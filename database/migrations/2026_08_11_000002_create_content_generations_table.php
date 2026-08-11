<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('status')->default('queued');

            // Stored so a result can be traced back to the exact prompt that
            // produced it — prompts change, and old output shouldn't be
            // attributed to the current template.
            $table->text('prompt');
            $table->longText('result')->nullable();
            $table->text('error')->nullable();

            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();

            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Polling hits "latest generations for this product" constantly.
            $table->index(['product_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_generations');
    }
};
