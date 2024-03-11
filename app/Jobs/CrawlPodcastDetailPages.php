<?php

namespace App\Jobs;

use App\Models\Podcast;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class CrawlPodcastDetailPages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $podcast_id)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $podcast = Podcast::findOrFail($this->podcast_id);

        collect($podcast->markup_detail_paths)->each(function ($path) use ($podcast) {
            $contents = Storage::disk(config('podcasts.disk'))->get($path);
            $crawler  = new Crawler($contents);
            $nodes    = $crawler->filter('main.content > article.post');

            $nodes->each(function (Crawler $node) use ($podcast) {
                $content  = $node->filter('.entry-content');
                $url_node = $content->filter('.powerpress_links_mp3 > a.powerpress_link_pinw')->first();

                if (!$url_node) {
                    return true;
                }

                $url = $url_node->attr('href');

                if (empty($url)) {
                    return true;
                }

                $episode = $podcast->episodes()->forSourceUrl($url)->first();

                if (!$episode) {
                    return true;
                }

                $meta            = $node->filter('.entry-header > .entry-meta > .entry-time')->first();
                $published_at    = $meta->attr('datetime');
                $published_at_ts = Carbon::createFromFormat('Y-m-d\TH:i:sP', $published_at);

                $description     = collect($content->filter('p:not(.powerpress_links)')->each(function (Crawler $node) {
                    $c = html_entity_decode($node->text(null, true), ENT_QUOTES, 'UTF-8');
                    $c = trim(preg_replace('/\xc2\xa0/', ' ', $c));
                    return Str::replace("[Read more…]", '', $c);
                }))->filter()->join("\n");

                $episode->update(['published_at' => $published_at_ts, 'description' => $description]);

                return true;
            });
        });
    }
}
