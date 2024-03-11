<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Episode extends Model implements Feedable
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'path', 'source_url', 'description', 'length', 'duration', 'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime'
    ];

    public function podcast(): BelongsTo
    {
        return $this->belongsTo(Podcast::class);
    }

    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
                       ->id(md5($this->slug))
                       ->title($this->title)
                       ->link(Storage::disk(config('podcasts.remote-disk'))->url($this->path))
                       ->updated($this->published_at ?? now())
                       ->summary($this->description ?? '')
                       ->enclosureLength($this->length ?? 22)
                       ->authorName('Frank Viola');
    }

    public function scopeForSourceUrl(Builder $query, string $url)
    {
        $parts = parse_url($url);
        $target = "//{$parts['host']}{$parts['path']}";

        $query->where('source_url', 'like', "%$target");
    }

    public function getPodcastFeedItems($owner_slug): Collection
    {
        if (empty($owner_slug)) {
            return new Collection();
        }

        $owner = Podcast::firstWhere('slug', $owner_slug);

        return Episode::whereNotNull('path')->whereBelongsTo($owner)->get();
    }
}
