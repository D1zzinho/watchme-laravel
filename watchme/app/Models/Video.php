<?php

namespace App\Models;

use App\Traits\Taggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use JetBrains\PhpStorm\ArrayShape;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Attributes\SearchUsingPrefix;
use Laravel\Scout\Searchable;

/**
 * @method static whereHashId(string $hashId)
 */
class Video extends Model
{
    use Searchable;
    use HasFactory;
    use Taggable;

    protected $with = ['status'];

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
        'duration',
        'converted_for_downloading_at',
        'converted_for_streaming_at',
    ];

    protected $dates = [
        'converted_for_downloading_at',
        'converted_for_streaming_at',
    ];

    public function getRouteKeyName(): string
    {
        return 'hash_id';
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function searchable(): bool
    {
        return $this->status->title === 'public'
            && !is_null($this->converted_for_downloading_at)
            && !is_null($this->converted_for_streaming_at);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    #[SearchUsingFullText(['title', 'description'])]
    public function toSearchableArray(): array
    {
        return [
            'id'          => $this->getKey(),
            'hash_id'     => $this->hash_id,
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status->title,
            'tags'        => $this->tags->pluck('name')
        ];
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
        return $this->belongsToMany(Source::class)
               ->withPivot('source_path')
               ->withTimestamps();
    }
}
