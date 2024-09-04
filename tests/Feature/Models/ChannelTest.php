<?php

namespace Tests\Feature\Models;

use App\Models\Channel;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChannelTest extends TestCase
{
    public function test_is_live_attribute_for_empty_items(): void
    {
        $channel = Channel::factory()->create();

        Http::fake(['https://www.googleapis.com/youtube/v3/search*' => []]);

        $this->assertFalse($channel->is_live);

        Http::assertSent(fn (Request $request) => str($request->url())->contains($channel->google_channel_id));
    }

    public function test_is_live_attribute_for_some_items(): void
    {
        $channel = Channel::factory()->create();

        Http::fake(['https://www.googleapis.com/youtube/v3/search*' => File::json(base_path('/tests/data/youtube-data-api/search-results.json'))]);

        $this->assertTrue($channel->is_live);

        Http::assertSent(fn (Request $request) => str($request->url())->contains($channel->google_channel_id));
    }

    public function test_current_video_id_attribute(): void
    {
        $channel = Channel::factory()->create();
        $response = File::json(base_path('/tests/data/youtube-data-api/search-results.json'));
        $items = $response['items'];

        Http::fake(['https://www.googleapis.com/youtube/v3/search*' => $response]);

        $this->assertEquals($items[0]['id']['videoId'], $channel->current_video_id);

        Http::assertSent(fn (Request $request) => str($request->url())->contains($channel->google_channel_id));
    }

    public function test_flush_cache(): void
    {
        $channel = Channel::factory()->create();

        Cache::shouldReceive('forget')
            ->once()
            ->with($channel->cache_key)
            ->andReturnTrue();

        $this->assertTrue($channel->flushCache());
    }
}
