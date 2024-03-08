<?php

namespace App\Jobs;

use App\Models\Podcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class CrawlPodcastsMarkup implements ShouldQueue
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
        $podcast  = Podcast::findOrFail($this->podcast_id);
        $contents = Storage::disk(config('podcasts.disk'))->get($podcast->markup_path);
        $crawler  = new Crawler($contents);
        $nodes    = $crawler->filter('p');

        $nodes->each(function (Crawler $node) use ($podcast) {
            $anchor = $node->filter('a')->eq(0);

            if (!$anchor->count()) {
                return true;
            }

            $href = $anchor->attr('href');

            if (!Str::endsWith($href, '.mp3')) {
                return true;
            }

            $lesson_url = $anchor->attr('href');
            $parts      = parse_url($lesson_url);
            $path       = $parts['path'];

            if (Str::startsWith($path, '/')) {
                $path = substr($path, 1);
            }

            $path_segments = collect(explode('/', $path));
            $target_series = null;
            $import_config = config('podcasts.shows.dcln.series');

            if ($path_segments->count() == 3) {
                $parent_segment = strtolower($path_segments->get(1));

                if (array_key_exists($parent_segment, $import_config)) {
                    $target_series = $parent_segment;
                }
            } else if ($path_segments->count() == 2) {
                //it's EP (they do not have this segment in the url)
                $target_series = 'ep';
            }

            if (empty($target_series)) {
                return true;
            }

            $destination_config = Arr::get($import_config, $target_series);
            $dest_file_name     = html_entity_decode($node->text(null, true), ENT_QUOTES, 'UTF-8'); // replace &nbsp; html entities
            $dest_file_name     = preg_replace('/\xc2\xa0/', ' ', $dest_file_name);
            $title              = $dest_file_name; // set internal title to the clean HTML node value
            $dest_file_name     = Str::replace(" – ", "-", strtolower($dest_file_name)); // swap out dash-spaces for dashes and lower case all
            $dest_file_name     = Str::replace(" ", "-", $dest_file_name); // swap out dash-spaces for dashes and lower case all
            $dest_file_name     = Str::replace("’", "", $dest_file_name); // replace weird apostrophes
            $dest_file_name     = Str::replace(":", "-", $dest_file_name); // replace weird apostrophes
            $file_path          = "{$destination_config['path']}/$dest_file_name.mp3";

            ImportPodcastEpisode::dispatch($podcast->id, $destination_config, $title, $file_path, $lesson_url);

            return true;
        });
    }
}
