<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can display posts index page', function () {
    $posts = Post::factory()->count(3)->create();

    $response = $this->actingAs($this->user)->get('/posts');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('posts/index')
        ->has('posts.data', 3)
    );
});

it('can display post create page', function () {
    $response = $this->actingAs($this->user)->get('/posts/create');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('posts/create')
    );
});

it('can store a new post', function () {
    $postData = [
        'name' => 'Test Post',
        'slug' => 'test-post',
        'content' => 'This is test content for the post.',
        'category' => 'Technology',
    ];

    $response = $this->actingAs($this->user)->post('/posts', $postData);

    $response->assertRedirect();
    $this->assertDatabaseHas('posts', [
        'name' => 'Test Post',
        'slug' => 'test-post',
        'content' => 'This is test content for the post.',
        'category' => 'Technology',
        'user_id' => $this->user->id,
    ]);
});

it('validates required fields when storing a post', function () {
    $response = $this->actingAs($this->user)->post('/posts', []);

    $response->assertSessionHasErrors(['name', 'content']);
});

it('can store a post without a slug', function () {
    $postData = [
        'name' => 'Test Post Without Slug',
        'content' => 'This is test content for the post.',
        'category' => 'Technology',
    ];

    $response = $this->actingAs($this->user)->post('/posts', $postData);

    $response->assertRedirect();
    $this->assertDatabaseHas('posts', [
        'name' => 'Test Post Without Slug',
        'content' => 'This is test content for the post.',
        'category' => 'Technology',
        'user_id' => $this->user->id,
    ]);
    
    $post = Post::where('name', 'Test Post Without Slug')->first();
    expect($post->slug)->toEqual('test-post-without-slug');
});

it('can display a single post', function () {
    $post = Post::factory()->create();

    $response = $this->actingAs($this->user)->get("/posts/{$post->slug}");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('posts/show')
        ->has('post')
    );
});

it('can display post edit page for owner', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)->get("/posts/{$post->slug}/edit");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('posts/edit')
        ->has('post')
    );
});

it('can update a post as owner', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id]);
    $updateData = [
        'name' => 'Updated Post Name',
        'slug' => $post->slug,
        'content' => 'Updated post content.',
        'category' => 'Updated Category',
    ];

    $response = $this->actingAs($this->user)->put("/posts/{$post->slug}", $updateData);

    $response->assertRedirect("/posts/{$post->slug}");
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'name' => 'Updated Post Name',
        'content' => 'Updated post content.',
        'category' => 'Updated Category',
    ]);
});

it('can delete a post as owner', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)->delete("/posts/{$post->slug}");

    $response->assertRedirect('/posts');
    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

it('cannot edit post that does not belong to user', function () {
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($this->user)->get("/posts/{$post->slug}/edit");

    $response->assertStatus(403);
});

it('cannot update post that does not belong to user', function () {
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $otherUser->id]);

    $updateData = [
        'name' => 'Hacked Post Name',
        'slug' => 'hacked-slug',
        'content' => 'Hacked content.',
        'category' => 'Hacked Category',
    ];

    $response = $this->actingAs($this->user)->put("/posts/{$post->slug}", $updateData);

    $response->assertStatus(403);
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'name' => $post->name,
        'content' => $post->content,
    ]);
});

it('cannot delete post that does not belong to user', function () {
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($this->user)->delete("/posts/{$post->slug}");

    $response->assertStatus(403);
    $this->assertDatabaseHas('posts', ['id' => $post->id]);
});

it('requires authentication for all routes', function () {
    $post = Post::factory()->create();

    $this->get('/posts')->assertRedirect('/login');
    $this->get('/posts/create')->assertRedirect('/login');
    $this->post('/posts')->assertRedirect('/login');
    $this->get("/posts/{$post->slug}")->assertRedirect('/login');
    $this->get("/posts/{$post->slug}/edit")->assertRedirect('/login');
    $this->put("/posts/{$post->slug}")->assertRedirect('/login');
    $this->delete("/posts/{$post->slug}")->assertRedirect('/login');
});

it('validates post data using StorePostRequest', function () {
    $response = $this->actingAs($this->user)->post('/posts', [
        'name' => '',
        'slug' => '',
        'content' => '',
    ]);

    $response->assertSessionHasErrors(['name', 'content']);
});

it('validates post data using UpdatePostRequest', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)->put("/posts/{$post->slug}", [
        'name' => '',
        'content' => '',
    ]);

    $response->assertSessionHasErrors(['name', 'content']);
});

it('authorizes edit request via ShowPostEditRequest', function () {
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($this->user)->get("/posts/{$post->slug}/edit");

    $response->assertStatus(403);
});

it('authorizes update request via UpdatePostRequest', function () {
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($this->user)->put("/posts/{$post->slug}", [
        'name' => 'Test',
        'slug' => 'test-slug',
        'content' => 'Test content',
    ]);

    $response->assertStatus(403);
});

it('authorizes delete request via DestroyPostRequest', function () {
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($this->user)->delete("/posts/{$post->slug}");

    $response->assertStatus(403);
});


it('uses provided slug when creating post with slug', function () {
    $postData = [
        'name' => 'Test Post Title',
        'slug' => 'custom-slug',
        'content' => 'This is test content for the post.',
        'category' => 'Technology',
    ];

    $response = $this->actingAs($this->user)->post('/posts', $postData);

    $response->assertRedirect();
    $this->assertDatabaseHas('posts', [
        'name' => 'Test Post Title',
        'slug' => 'custom-slug',
        'user_id' => $this->user->id,
    ]);
});

it('validates slug uniqueness when creating post', function () {
    Post::factory()->create(['slug' => 'duplicate-slug']);

    $postData = [
        'name' => 'Test Post Title',
        'slug' => 'duplicate-slug',
        'content' => 'This is test content for the post.',
    ];

    $response = $this->actingAs($this->user)->post('/posts', $postData);

    $response->assertSessionHasErrors(['slug']);
});

it('can access post by slug', function () {
    $post = Post::factory()->create(['slug' => 'test-slug']);

    $response = $this->actingAs($this->user)->get('/posts/test-slug');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('posts/show')
        ->has('post')
        ->where('post.slug', 'test-slug')
    );
});

it('can update post slug', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id, 'slug' => 'old-slug']);

    $updateData = [
        'name' => $post->name,
        'slug' => 'new-slug',
        'content' => $post->content,
        'category' => $post->category,
    ];

    $response = $this->actingAs($this->user)->put('/posts/old-slug', $updateData);

    $response->assertRedirect('/posts/new-slug');
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'slug' => 'new-slug',
    ]);
});

it('validates slug uniqueness when updating post', function () {
    $existingPost = Post::factory()->create(['slug' => 'existing-slug']);
    $postToUpdate = Post::factory()->create(['user_id' => $this->user->id, 'slug' => 'original-slug']);

    $updateData = [
        'name' => $postToUpdate->name,
        'slug' => 'existing-slug',
        'content' => $postToUpdate->content,
        'category' => $postToUpdate->category,
    ];

    $response = $this->actingAs($this->user)->put('/posts/original-slug', $updateData);

    $response->assertSessionHasErrors(['slug']);
});

it('can search posts by name', function () {
    Post::factory()->create(['name' => 'Laravel Tutorial', 'content' => 'Learning Laravel basics']);
    Post::factory()->create(['name' => 'React Guide', 'content' => 'Building React apps']);
    Post::factory()->create(['name' => 'Vue.js Basics', 'content' => 'Getting started with Vue']);

    $response = $this->actingAs($this->user)->get('/posts?search=Laravel');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('posts/index')
        ->has('posts.data', 1)
        ->where('posts.data.0.name', 'Laravel Tutorial')
        ->where('filters.search', 'Laravel')
    );
});

it('can search posts by content', function () {
    Post::factory()->create(['name' => 'Tutorial One', 'content' => 'This covers React development']);
    Post::factory()->create(['name' => 'Tutorial Two', 'content' => 'This covers Laravel development']);
    Post::factory()->create(['name' => 'Tutorial Three', 'content' => 'This covers Vue development']);

    $response = $this->actingAs($this->user)->get('/posts?search=React');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('posts/index')
        ->has('posts.data', 1)
        ->where('posts.data.0.name', 'Tutorial One')
        ->where('filters.search', 'React')
    );
});

it('can search posts by category', function () {
    Post::factory()->create(['name' => 'Post One', 'category' => 'Technology']);
    Post::factory()->create(['name' => 'Post Two', 'category' => 'Business']);
    Post::factory()->create(['name' => 'Post Three', 'category' => 'Technology']);

    $response = $this->actingAs($this->user)->get('/posts?search=Technology');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('posts/index')
        ->has('posts.data', 2)
        ->where('filters.search', 'Technology')
    );
});

it('returns empty results for non-matching search', function () {
    Post::factory()->count(3)->create();

    $response = $this->actingAs($this->user)->get('/posts?search=nonexistent');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('posts/index')
        ->has('posts.data', 0)
        ->where('filters.search', 'nonexistent')
    );
});

it('returns all posts when search is empty', function () {
    Post::factory()->count(3)->create();

    $response = $this->actingAs($this->user)->get('/posts');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('posts/index')
        ->has('posts.data', 3)
        ->where('filters.search', '')
    );
});

it('maintains pagination with search results', function () {
    // Create 12 posts with Laravel in the name
    for ($i = 1; $i <= 12; $i++) {
        Post::factory()->create(['name' => "Laravel Tutorial {$i}"]);
    }

    // Create some posts without Laravel
    Post::factory()->count(3)->create(['name' => 'React Tutorial']);

    $response = $this->actingAs($this->user)->get('/posts?search=Laravel');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('posts/index')
        ->has('posts.data', 10) // Should be limited to 10 per page
        ->where('posts.total', 12)
        ->where('posts.last_page', 2)
        ->where('filters.search', 'Laravel')
    );
});

