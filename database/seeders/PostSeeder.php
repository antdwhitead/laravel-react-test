<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $count = User::query()->count();
        // Ensure we have some users to associate with posts and comments
        if ($count < 10) {
            User::factory(10 - $count)->create();
        }

        $users = User::all();

        // Create posts over the last 30 days
        for ($day = 30; $day >= 0; $day--) {
            $date = Carbon::now()->subDays($day);

            // Random number of posts per day (3-10)
            $postsPerDay = random_int(3, 10);

            for ($i = 0; $i < $postsPerDay; $i++) {
                // Create random time during the day
                $randomTime = $date->copy()->addHours(random_int(0, 23))->addMinutes(random_int(0, 59));

                $post = Post::factory()->create([
                    'user_id' => $users->random()->id,
                    'created_at' => $randomTime,
                    'updated_at' => $randomTime,
                ]);

                // Random number of comments per post (3-10)
                $commentsPerPost = random_int(3, 10);

                for ($j = 0; $j < $commentsPerPost; $j++) {
                    // Comments should be created after the post, within a reasonable timeframe
                    $commentTime = $randomTime->copy()->addMinutes(random_int(5, 1440)); // 5 minutes to 24 hours after post

                    Comment::factory()->create([
                        'post_id' => $post->id,
                        'user_id' => $users->random()->id,
                        'created_at' => $commentTime,
                        'updated_at' => $commentTime,
                    ]);
                }
            }
        }
    }
}
