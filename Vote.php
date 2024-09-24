<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $table = 'votes';

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'user_id',
        'vote_item_id',
        'is_dislike',
    ];

    /**
     * @inheritdoc
     */
    protected $casts = [
        'user_id'      => 'integer',
        'vote_item_id' => 'integer',
        'is_dislike'   => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function voteItem()
    {
        return $this->belongsTo(VoteItem::class);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeLike(Builder $query): Builder
    {
        return $query->where('is_dislike', false);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeDislike(Builder $query): Builder
    {
        return $query->where('is_dislike', true);
    }
}
