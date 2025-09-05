<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyPostRequest;
use App\Http\Requests\ShowPostEditRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->get('search', '');

        $posts = Post::with(['user', 'comments'])
            ->when($search, fn($query) => $query->search($search))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('posts/index', [
            'posts' => $posts,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('posts/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $post = $request->user()->posts()->create($validated);

        return redirect()->route('posts.show', $post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): Response
    {
        $post->load(['user', 'comments.user']);

        return Inertia::render('posts/show', [
            'post' => $post,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShowPostEditRequest $request, Post $post): Response
    {
        return Inertia::render('posts/edit', [
            'post' => $post,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $validated = $request->validated();

        $post->update($validated);

        return redirect()->route('posts.show', $post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyPostRequest $request, Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('posts.index');
    }
}
