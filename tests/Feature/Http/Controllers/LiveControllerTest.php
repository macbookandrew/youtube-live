<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Channel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LiveControllerTest extends TestCase
{
    public function test_route_uses_hashid(): void
    {
        $channel = Channel::factory()->create();

        $this
            ->get(route('channel.live', ['channel' => $channel->id]))
            ->assertNotFound();
    }

    public function test_fallback_content_is_displayed(): void
    {
        $channel = Channel::factory()->create([
            'fallback_image' => null,
            'fallback_video' => null,
        ]);

        Http::fake(['https://www.googleapis.com/youtube/v3/search*' => []]);

        $this
            ->get(route('channel.live', ['channel' => $channel]))
            ->assertOk()
            ->assertSee('No live content');
    }

    public function test_fallback_image_is_displayed(): void
    {
        $channel = Channel::factory()->create([
            'fallback_image' => fake()->imageUrl(),
            'fallback_video' => null,
        ]);

        Http::fake(['https://www.googleapis.com/youtube/v3/search*' => []]);

        $this
            ->get(route('channel.live', ['channel' => $channel]))
            ->assertOk()
            ->assertSee($channel->fallback_image)
            ->assertDontSee($channel->fallback_video);
    }

    public function test_fallback_video_is_displayed(): void
    {
        $channel = Channel::factory()->create([
            'fallback_image' => fake()->imageUrl(),
            'fallback_video' => 'https://www.youtube.com/watch?v=GFq6wH5JR2A',
        ]);

        Http::fake(['https://www.googleapis.com/youtube/v3/search*' => []]);

        $this
            ->get(route('channel.live', ['channel' => $channel]))
            ->assertOk()
            ->assertDontSee($channel->fallback_image)
            ->assertSeeInOrder([
                'iframe',
                str($channel->fallback_video)->after('v='),
            ]);
    }

    public function test_live_video_is_displayed(): void
    {
        $channel = Channel::factory()->create([
            'fallback_image' => fake()->imageUrl(),
            'fallback_video' => 'https://www.youtube.com/watch?v=GFq6wH5JR2A',
        ]);

        $response = File::json(base_path('/tests/data/youtube-data-api/search-results.json'));
        $items = $response['items'];

        Http::fake(['https://www.googleapis.com/youtube/v3/search*' => $response]);

        $this
            ->get(route('channel.live', ['channel' => $channel]))
            ->assertOk()
            ->assertDontSee([
                $channel->fallback_image,
                str($channel->fallback_video)->after('v='),
            ])
            ->assertSeeInOrder([
                'iframe',
                $items[0]['id']['videoId'],
            ]);
    }
}
