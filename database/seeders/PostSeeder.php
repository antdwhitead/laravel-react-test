<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have some users to associate with posts and comments
        if (User::query()->count() === 0) {
            User::factory(10)->create();
        }

        $users = User::all();

        // Create 100 posts
        Post::factory(100)->create()->each(function (Post $post) use ($users) {
            // Random number of comments between 5 and 10
            $commentCount = random_int(5, 10);


            // Create comments for this post
            Comment::factory($commentCount)->create([
                'post_id' => $post->id,
                'user_id' => $users->random()->id,
            ]);
        });
    }
}
