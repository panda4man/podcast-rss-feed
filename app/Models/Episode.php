<?php

namespace App\Models;

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
        'title', 'slug', 'path', 'source_url', 'description'
    ];

    public function podcast(): BelongsTo
    {
        return $this->belongsTo(Podcast::class);
    }

    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
                       ->id(Storage::disk(config('podcasts.remote-disk'))->url($this->path))
                       ->title($this->title)
                       ->link(Storage::disk(config('podcasts.remote-disk'))->url($this->path))
                       ->updated($this->updated_at)
                       ->summary('tbd')
                       ->authorName('Frank Viola');
    }

    public function getPodcastFeedItems($owner_slug): Collection
    {
        if(empty($owner_slug)) {
            return new Collection();
        }

        $owner = Podcast::firstWhere('slug', $owner_slug);

        return Episode::whereNotNull('path')->whereBelongsTo($owner)->get();
    }
}
