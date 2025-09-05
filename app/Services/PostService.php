<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;

class PostService
{
    /**
     * Generate a unique slug for a post.
     */
    public function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug ?: 'untitled';
        $counter = 1;

        $query = Post::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
            $query = Post::where('slug', $slug);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }
}