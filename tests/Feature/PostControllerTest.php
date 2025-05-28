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
        'content' => 'This is test content for the post.',
    ];

    $response = $this->actingAs($this->user)->post('/posts', $postData);

    $response->assertRedirect();
    $this->assertDatabaseHas('posts', [
        'name' => 'Test Post',
        'content' => 'This is test content for the post.',
        'user_id' => $this->user->id,
    ]);
});

it('validates required fields when storing a post', function () {
    $response = $this->actingAs($this->user)->post('/posts', []);

    $response->assertSessionHasErrors(['name', 'content']);
});

it('can display a single post', function () {
    $post = Post::factory()->create();

    $response = $this->actingAs($this->user)->get("/posts/{$post->id}");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('posts/show')
        ->has('post')
    );
});

it('can display post edit page for owner', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)->get("/posts/{$post->id}/edit");

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
        'content' => 'Updated post content.',
    ];

    $response = $this->actingAs($this->user)->put("/posts/{$post->id}", $updateData);

    $response->assertRedirect("/posts/{$post->id}");
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'name' => 'Updated Post Name',
        'content' => 'Updated post content.',
    ]);
});

it('can delete a post as owner', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)->delete("/posts/{$post->id}");

    $response->assertRedirect('/posts');
    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

it('requires authentication for all routes', function () {
    $post = Post::factory()->create();

    $this->get('/posts')->assertRedirect('/login');
    $this->get('/posts/create')->assertRedirect('/login');
    $this->post('/posts')->assertRedirect('/login');
    $this->get("/posts/{$post->id}")->assertRedirect('/login');
    $this->get("/posts/{$post->id}/edit")->assertRedirect('/login');
    $this->put("/posts/{$post->id}")->assertRedirect('/login');
    $this->delete("/posts/{$post->id}")->assertRedirect('/login');
});
