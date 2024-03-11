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

class ScrapePodcastDetailPages implements ShouldQueue
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
        $folder  = $podcast->htmlFolder();

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

        $page     = 1;
        $base_url = 'https://www.thedeeperchristianlife.com/category/masterclass/';
        $url      = $base_url;

        $response2 = $client->get($url, [
            'allow_redirects' => ['track_redirects' => true], 'cookies' => $cookieJar
        ]);

        $fetched_paths = [];

        while (empty($response2->getHeader(\GuzzleHttp\RedirectMiddleware::HISTORY_HEADER))) {
            if ($response2->getStatusCode() >= 200 && $response2->getStatusCode() <= 299) {
                $file = "$folder/content-detail-page-$page.html";
                $res  = Storage::disk(config('podcasts.disk'))->put($file, $response2->getBody()->getContents());

                if ($res) {
                    $fetched_paths[] = $file;
                }
            }

            $page++;

            if ($page > 1) {
                $url = "$base_url" . "page/$page/";
            }

            $response2 = $client->get($url, [
                'allow_redirects' => ['track_redirects' => true], 'cookies' => $cookieJar
            ]);
        }

        $podcast->update(['markup_detail_paths' => $fetched_paths]);
    }
}
