<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'content',
        'category',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = static::generateUniqueSlug($post->name);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('name') && empty($post->slug)) {
                $post->slug = static::generateUniqueSlug($post->name);
            }
        });
    }

    /**
     * Generate a unique slug from the given name.
     */
    public static function generateUniqueSlug(string $name, ?int $id = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug ?: 'untitled';
        $counter = 1;

        $query = static::where('slug', $slug);
        if ($id) {
            $query->where('id', '!=', $id);
        }

        while ($query->exists()) {
            $slug = ($baseSlug ?: 'untitled').'-'.$counter;
            $counter++;
            $query = static::where('slug', $slug);
            if ($id) {
                $query->where('id', '!=', $id);
            }
        }

        return $slug;
    }

    /**
     * Get the user that owns the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for the post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
