<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportPodcastEpisode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $podcast_id, public array $episode_config, public string $title, public string $file_path, public string $source_url)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $podcast = Podcast::findOrFail($this->podcast_id);
        $episode = Episode::whereBelongsTo($podcast)->where('source_url', $this->source_url)->first();

        if (!$episode) {
            $episode = Episode::make();
            $episode->podcast()->associate($podcast);
            $episode->fill([
                'source_url' => $this->source_url,
            ]);
        }

        $episode->fill([
            'title' => $this->title,
            'slug'  => Str::slug($this->title)
        ])->save();

        if (!$episode->length
            && $episode->path
            && Storage::disk(config('podcasts.remote-disk'))->exists($episode->path)) {
            $this->getMetadata(Storage::disk(config('podcasts.remote-disk'))->url($episode->path), $episode);
        }

        if ($episode->path && Storage::disk(config('podcasts.remote-disk'))->exists($episode->path)) {
            return;
        }

        $lesson  = file_get_contents($this->source_url);
        $success = Storage::disk(config('podcasts.remote-disk'))->put($this->file_path, $lesson);

        if (!$success) {
            $this->fail(new \Exception("Could not upload {$this->file_path} to S3"));

            return;
        }

        $episode->update(['path' => $this->file_path]);

        $this->getMetadata(Storage::disk(config('podcasts.remote-disk'))->url($episode->path), $episode);
    }

    private function getMetadata(string $target_url, Episode $episode): void
    {
        $res = Http::get('https://mp3-metadata.andrewclinton.dev/api/metadata', [
            'url' => Storage::disk(config('podcasts.remote-disk'))->url($episode->path)
        ]);

        if ($res->successful()) {
            $json = $res->json();
            $episode->update([
                'length' => $json['data']['length'] ?? null
            ]);
        }
    }
}
