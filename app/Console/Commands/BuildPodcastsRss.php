<?php

namespace App\Console\Commands;

use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Console\Command;

class BuildPodcastsRss extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build configured podcasts\' rss feeds';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Podcast::query();
        $bar   = $this->output->createProgressBar($query->count());

        $query->eachById(function (Podcast $podcast) use ($bar) {
            $podcast->episodes()->eachById(function (Episode $episode) {

            });
            $bar->advance();
        });

        $bar->finish();
        $this->output->newLine();
    }
}
