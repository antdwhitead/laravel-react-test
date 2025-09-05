<?php

namespace App\Observers;

use App\Models\Post;

class PostObserver
{
    /**
     * Handle the Post "creating" event.
     */
    public function creating(Post $post): void
    {
        if (empty($post->slug)) {
            $post->slug = Post::generateUniqueSlug($post->name);
        }
    }

    /**
     * Handle the Post "updating" event.
     */
    public function updating(Post $post): void
    {
        if ($post->isDirty('name') && empty($post->slug)) {
            $post->slug = Post::generateUniqueSlug($post->name, $post->id);
        }
    }
}
