<?php

namespace App\Models;

use App\Observers\PostObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[ObservedBy([PostObserver::class])]
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
     * Scope a query to search posts by term.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('content', 'like', "%{$term}%")
              ->orWhere('category', 'like', "%{$term}%");
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
            $slug = "{$baseSlug}-{$counter}";
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
