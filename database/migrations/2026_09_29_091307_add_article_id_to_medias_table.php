<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medias', function (Blueprint $table) {
            if (!Schema::hasColumn('medias', 'article_id')) {
                $table->foreignId('article_id')
                      ->nullable()
                      ->after('projet_id')
                      ->constrained('articles')
                      ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medias', function (Blueprint $table) {
            if (Schema::hasColumn('medias', 'article_id')) {
                $table->dropForeign(['article_id']);
                $table->dropColumn('article_id');
            }
        });
    }
};