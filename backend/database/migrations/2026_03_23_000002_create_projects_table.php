<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('author');
            $table->string('title');
            $table->text('description');
            $table->string('technologies')->nullable(); // Guardadas como string separado por comas
            $table->json('images')->nullable(); // Guardadas como array JSON (hasta 3)
            $table->string('project_url')->nullable();
            $table->string('repo_url')->nullable();
            $table->integer('year');
            $table->integer('winner')->default(0); // 0: no, 1, 2, 3: posición
            $table->integer('likes')->default(0);
            $table->integer('comments_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
