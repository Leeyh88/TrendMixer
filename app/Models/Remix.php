<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Remix extends Model
{
    // 데이터 삽입 허용 컬럼
    protected $fillable = [
        'music_track_id', 
        'genre_id', 
        'title', 
        'youtube_url', 
        'youtube_video_id', 
        'description',
        'view_count',
        'vote_count'
    ];

    /**
     * 장르와의 관계 (N:1)
     * 리믹스는 하나의 장르에 속합니다.
     */
    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    /**
     * 원곡과의 관계 (N:1)
     * 리믹스는 하나의 원곡에 속합니다.
     */
    public function musicTrack(): BelongsTo
    {
        return $this->belongsTo(MusicTrack::class);
    }
}