<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\PostService;

class PostObserver
{
    public function __construct(
        private PostService $postService
    ) {}

    /**
     * Handle the Post "creating" event.
     */
    public function creating(Post $post): void
    {
        if (empty($post->slug)) {
            $post->slug = $this->postService->generateUniqueSlug($post->name);
        }
    }

    /**
     * Handle the Post "updating" event.
     */
    public function updating(Post $post): void
    {
        if ($post->isDirty('name') && empty($post->slug)) {
            $post->slug = $this->postService->generateUniqueSlug($post->name, $post->id);
        }
    }
}
