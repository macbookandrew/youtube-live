<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;

/**
 * @property int $id
 * @property string $google_channel_id
 * @property string $name
 * @property string|null $fallback_image
 * @property string|null $fallback_video
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $cache_key
 * @property-read string $hashid
 * @property-read string $current_video_id
 * @property-read bool $is_live
 *
 * @method static \Database\Factories\ChannelFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Channel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Channel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Channel query()
 * @method static \Illuminate\Database\Eloquent\Builder|Channel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Channel whereFallbackImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Channel whereFallbackVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Channel whereGoogleChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Channel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Channel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Channel whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Channel extends Model
{
    use HasFactory;
    use HasHashid;
    use HashidRouting;

    /** @return ?Collection<int, array{kind: string, etag: string, id: array{kind: string, videoId: string}, snippet: array}> */
    public function liveVideos(): ?Collection
    {
        return Cache::remember($this->cache_key, now()->addMinutes(15), fn () => Http::get('https://www.googleapis.com/youtube/v3/search', [
            'key' => config('services.google.api_key'),
            'channelId' => $this->google_channel_id,
            'part' => 'id,snippet',
            'type' => 'video',
            'eventType' => 'live',
            'maxResults' => 1,
        ])->collect('items'));
    }

    public function flushCache(): bool
    {
        return Cache::forget($this->cache_key);
    }

    protected function currentVideoId(): Attribute
    {
        return Attribute::make(
            get: fn () => data_get($this->liveVideos()->first(), 'id.videoId'),
        );
    }

    protected function isLive(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->liveVideos()->isNotEmpty(),
        );
    }

    protected function cacheKey(): Attribute
    {
        return Attribute::make(
            get: fn () => 'channel-'.$this->hashid,
        );
    }
}
