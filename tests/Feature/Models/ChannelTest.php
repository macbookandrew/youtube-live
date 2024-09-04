<?php

namespace Tests\Feature\Models;

use App\Models\Channel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ChannelTest extends TestCase
{
    public function test_is_live_attribute_for_empty_items(): void
    {
        $channel = Channel::factory()->create();

        Cache::shouldReceive('remember')
            ->once()
            ->withSomeOfArgs($channel->cache_key)
            ->andReturn(collect());

        $this->assertFalse($channel->is_live);
    }

    public function test_is_live_attribute_for_some_items(): void
    {
        $channel = Channel::factory()->create();

        Cache::shouldReceive('remember')
            ->once()
            ->withSomeOfArgs($channel->cache_key)
            ->andReturn(collect(
                File::json(base_path('/tests/data/youtube-data-api/search-results.json'))['items']
            ));

        $this->assertTrue($channel->is_live);
    }

    public function test_current_video_id_attribute(): void
    {
        $channel = Channel::factory()->create();

        Cache::shouldReceive('remember')
            ->once()
            ->withSomeOfArgs($channel->cache_key)
            ->andReturn(collect(
                $items = File::json(base_path('/tests/data/youtube-data-api/search-results.json'))['items']
            ));

        $this->assertEquals($items[0]['id']['videoId'], $channel->current_video_id);
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
