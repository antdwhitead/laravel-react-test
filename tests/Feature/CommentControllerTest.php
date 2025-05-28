<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->post = Post::factory()->create();
});

it('can display comments index page', function () {
    $comments = Comment::factory()->count(3)->create();

    $response = $this->actingAs($this->user)->get('/comments');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('comments/index')
        ->has('comments.data', 3)
    );
});

it('can display comment create page', function () {
    $response = $this->actingAs($this->user)->get('/comments/create');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('comments/create')
        ->has('posts')
    );
});

it('can store a new comment', function () {
    $commentData = [
        'post_id' => $this->post->id,
        'content' => 'This is a test comment.',
    ];

    $response = $this->actingAs($this->user)->post('/comments', $commentData);

    $response->assertRedirect("/posts/{$this->post->id}");
    $this->assertDatabaseHas('comments', [
        'post_id' => $this->post->id,
        'content' => 'This is a test comment.',
        'user_id' => $this->user->id,
    ]);
});

it('validates required fields when storing a comment', function () {
    $response = $this->actingAs($this->user)->post('/comments', []);

    $response->assertSessionHasErrors(['post_id', 'content']);
});

it('validates post exists when storing a comment', function () {
    $commentData = [
        'post_id' => 999999,
        'content' => 'This is a test comment.',
    ];

    $response = $this->actingAs($this->user)->post('/comments', $commentData);

    $response->assertSessionHasErrors(['post_id']);
});

it('can display a single comment', function () {
    $comment = Comment::factory()->create();

    $response = $this->actingAs($this->user)->get("/comments/{$comment->id}");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('comments/show')
        ->has('comment')
    );
});

it('can display comment edit page for owner', function () {
    $comment = Comment::factory()->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)->get("/comments/{$comment->id}/edit");

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('comments/edit')
        ->has('comment')
    );
});

it('can update a comment as owner', function () {
    $comment = Comment::factory()->create(['user_id' => $this->user->id]);
    $updateData = [
        'content' => 'Updated comment content.',
    ];

    $response = $this->actingAs($this->user)->put("/comments/{$comment->id}", $updateData);

    $response->assertRedirect("/comments/{$comment->id}");
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'content' => 'Updated comment content.',
    ]);
});

it('can delete a comment as owner', function () {
    $comment = Comment::factory()->create(['user_id' => $this->user->id]);
    $post = $comment->post;

    $response = $this->actingAs($this->user)->delete("/comments/{$comment->id}");

    $response->assertRedirect("/posts/{$post->id}");
    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

it('requires authentication for all routes', function () {
    $comment = Comment::factory()->create();

    $this->get('/comments')->assertRedirect('/login');
    $this->get('/comments/create')->assertRedirect('/login');
    $this->post('/comments')->assertRedirect('/login');
    $this->get("/comments/{$comment->id}")->assertRedirect('/login');
    $this->get("/comments/{$comment->id}/edit")->assertRedirect('/login');
    $this->put("/comments/{$comment->id}")->assertRedirect('/login');
    $this->delete("/comments/{$comment->id}")->assertRedirect('/login');
});
