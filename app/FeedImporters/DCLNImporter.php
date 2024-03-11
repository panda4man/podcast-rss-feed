<?php

namespace App\FeedImporters;

use App\Jobs\CrawlPodcastDetailPages;
use App\Jobs\CrawlPodcastsListingMarkup;
use App\Jobs\ScrapePodcastDetailPages;
use App\Jobs\ScrapePodcastListingMarkup;
use App\Models\Podcast;
use Illuminate\Support\Facades\Bus;

class DCLNImporter implements ImporterContract
{
    public function import(): void
    {
        // change to be a slug or something...
        $podcast = Podcast::find(2);

        Bus::chain([
            new ScrapePodcastListingMarkup($podcast->id),
            new CrawlPodcastsListingMarkup($podcast->id),
            new ScrapePodcastDetailPages($podcast->id),
            new CrawlPodcastDetailPages($podcast->id)
        ])->dispatch();
    }
}
