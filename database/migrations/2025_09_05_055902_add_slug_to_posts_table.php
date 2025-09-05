<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
        Post::whereNull('slug')->orWhere('slug', '')->chunkById(100, function ($posts) {
            foreach ($posts as $post) {
                $baseSlug = Str::slug($post->name);
                $slug = $baseSlug;
                $counter = 1;

                while (Post::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

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
