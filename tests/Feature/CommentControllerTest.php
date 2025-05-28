<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->post = Post::factory()->create();
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

it('requires authentication for comment routes', function () {
    $this->post('/comments')->assertRedirect('/login');
});
