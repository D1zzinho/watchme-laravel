<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static whereHashId(string $hashId)
 */
class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'video_status_id',
        'hash_id',
        'title',
        'description',
        'thumbnail',
        'preview',
        'views',
        'width',
        'height',
        'duration'
    ];

    public function getRouteKeyName(): string
    {
        return 'hash_id';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(VideoStatus::class, 'video_status_id', 'id');
    }

    public function sources(): BelongsToMany
    {
        return $this->belongsToMany(Source::class)->withPivot('source_path');
    }
}
