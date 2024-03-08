<?php

namespace App\Console\Commands;


use App\FeedImporters\ImporterContract;
use Illuminate\Console\Command;

class ScrapePodcasts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcasts:scrape';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape HTML-based podcasts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $shows = config('podcasts.shows');
        $bar   = $this->output->createProgressBar(count(array_keys($shows)));

        foreach (config('podcasts.shows') as $key => $config) {
            if (!isset($config['importer'])) {
                $bar->advance();
                continue;
            }

            /** @var ImporterContract $importer */
            $importer = new $config['importer'];
            $importer->import();
            $bar->advance();
        }

        $bar->finish();
        $this->output->newLine();
    }
}
