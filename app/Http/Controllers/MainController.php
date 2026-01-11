<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Remix;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        // 1. Hot Trends: 리믹스 곡 중 track_title, artist_name 기준 중복이 많은 순 (인기 재료)
        $hotTrends = Remix::select('track_title', 'artist_name', 'album_cover_url', DB::raw('count(*) as total'))
            ->groupBy('track_title', 'artist_name', 'album_cover_url')
            ->orderByDesc('total')
            ->take(4)
            ->get();

        // 2. Latest Feedback: posts 테이블의 가장 최근글 5개
        $latestFeedback = Post::with(['user', 'remix'])
            ->latest()
            ->take(5)
            ->get();

        // 3. Leaderboard: remix 테이블에서 vote_count(좋아요/투표)가 많은 순 5개
        $leaderboard = Remix::with('user')
            ->orderByDesc('vote_count')
            ->take(5)
            ->get();

        return Inertia::render('Welcome', [
            'hotTrends' => $hotTrends,
            'latestFeedback' => $latestFeedback,
            'leaderboard' => $leaderboard,
        ]);
    }
}