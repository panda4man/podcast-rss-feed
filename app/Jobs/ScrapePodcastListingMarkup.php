<?php

namespace App\Jobs;

use App\Models\Podcast;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ScrapePodcastListingMarkup implements ShouldQueue
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
        $podcast   = Podcast::findOrFail($this->podcast_id);
        $folder    = "podcast-{$podcast->id}";
        $file      = "$folder/content.html";

        $client    = new Client();
        $cookieJar = new CookieJar();

        $response = $client->post($podcast->login_url, [
                'form_params'     => [
                    'log' => $podcast->username,
                    'pwd' => $podcast->password,
                ],
                'headers'         => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36'
                ],
                'allow_redirects' => true,
                'cookies'         => $cookieJar
            ]
        );

        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            $this->fail(new \Exception("Could not login to podcast website"));

            return;
        }

        $response2 = $client->get($podcast->website_url, [
            'allow_redirects' => true, 'cookies' => $cookieJar
        ]);

        $res = Storage::disk(config('podcasts.disk'))->put($file, $response2->getBody()->getContents());

        if($res) {
            $podcast->update(['markup_listing_path' => $file]);
        }
    }
}
