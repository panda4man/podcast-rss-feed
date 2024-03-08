<?php

namespace App\Console\Commands;

use App\Models\Podcast;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class AddPodcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcast:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a podcast to the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $podcast  = Podcast::make();
        $title    = $this->ask('title');
        $url      = $this->ask('website_url');
        $username = $this->ask('username');
        $password = $this->secret('password');

        $podcast->fill([
            'title'       => $title,
            'website_url' => $url,
            'username'    => $username,
            'password'    => Crypt::encryptString($password)
        ]);

        $podcast->save();
    }
}
