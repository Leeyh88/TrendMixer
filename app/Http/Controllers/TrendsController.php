<?php

namespace App\Http\Controllers;
use App\Models\Trend;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrendsController extends Controller
{
    public function index()
    {
        // 유튜브 데이터 4종 세트
        $charts = [
            'youtube_kr_songs' => Trend::where('platform', 'youtube')->where('category', 'kr_songs')->orderBy('rank')->get(),
            'youtube_us_songs' => Trend::where('platform', 'youtube')->where('category', 'us_songs')->orderBy('rank')->get(),
            'youtube_kr_trend' => Trend::where('platform', 'youtube')->where('category', 'kr_trend')->orderBy('rank')->get(),
            'youtube_us_trend' => Trend::where('platform', 'youtube')->where('category', 'us_trend')->orderBy('rank')->get(),
            
            // 스포티파이/사클은 아직 없으므로 빈 배열
            'spotify' => [],
            'soundcloud' => [],
        ];

        return Inertia::render('Trends/Index', [
            'charts' => $charts
        ]);
    }
}
