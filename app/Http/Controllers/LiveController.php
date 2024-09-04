<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Cohensive\OEmbed\Facades\OEmbed;
use Illuminate\Http\Request;

class LiveController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Channel $channel)
    {
        if (! $channel->is_live) {
            return view('live-controller', [
                'fallbackImage' => $channel->fallback_image,
                'fallbackVideo' => $channel->fallback_video ? OEmbed::get($channel->fallback_video)->html() : null,
            ]);
        }

        $embed = OEmbed::get('https://youtu.be/'.$channel->current_video_id);

        return view('live-controller', [
            'liveMarkup' => $embed->html(),
        ]);
    }
}
