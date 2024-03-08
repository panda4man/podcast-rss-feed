<?php

namespace App\FeedImporters;

use App\Jobs\CrawlPodcastsMarkup;
use App\Jobs\ScrapePodcastMarkup;
use App\Models\Podcast;
use Illuminate\Support\Facades\Bus;

class DCLNImporter implements ImporterContract
{
    public function import(): void
    {
        // change to be a slug or something...
        $podcast = Podcast::find(2);

        Bus::chain([
            new ScrapePodcastMarkup($podcast->id),
            new CrawlPodcastsMarkup($podcast->id)
        ])->dispatch();
    }
}
