<?php

use App\Models\Post;
use App\Services\PostService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First add the column as nullable
        Schema::table('posts', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // Populate slugs for existing posts
        $postService = app(PostService::class);
        Post::whereNull('slug')->orWhere('slug', '')->chunkById(100, function ($posts) use ($postService) {
            foreach ($posts as $post) {
                $slug = $postService->generateUniqueSlug($post->name, $post->id);
                $post->update(['slug' => $slug]);
            }
        });

        // Now make the column required and unique
        Schema::table('posts', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
