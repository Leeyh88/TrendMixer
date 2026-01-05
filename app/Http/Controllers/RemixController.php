<?php

namespace App\Http\Controllers;

use App\Models\Remix;
use App\Models\Genre;
use App\Models\MusicTrack;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class RemixController extends Controller
{
    // 리믹스 목록 페이지
    public function index(Request $request) 
    {
        $query = Remix::with(['genre', 'musicTrack', 'user']);

        // 1. 장르 필터링
        if ($request->genre) {
            $query->whereHas('genre', function($q) use ($request) {
                $q->where('slug', $request->genre);
            });
        }

        // 2. 정렬 (최신순 vs 인기순)
        if ($request->sort === 'popular') {
            $query->orderBy('vote_count', 'desc');
        } else {
            $query->latest();
        }

        return Inertia::render('Remixes/Index', [
            'remixes' => $query->paginate(12)->withQueryString(), // 페이징 추가
            'genres' => Genre::all(),
            'filters' => $request->only(['genre', 'sort'])
        ]);
    }

    // 업로드 페이지 보여주기
    public function create()
    {
        return Inertia::render('Remixes/Create', [
            'genres' => Genre::all(),
            'musicTracks' => MusicTrack::all(),
        ]);
    }

    // 저장 로직 (마이그레이션 구조 반영)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'youtube_url' => 'required|url',
            'genre_id' => 'required|exists:genres,id',
            'music_track_id' => 'required|exists:music_tracks,id',
        ]);

        // 유튜브 비디오 ID 추출 (간단한 정규식 버전)
        preg_match("/(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be\/)[^&\n]+/", $request->youtube_url, $matches);
        $videoId = $matches[0] ?? null;

        Remix::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'youtube_url' => $request->youtube_url,
            'youtube_video_id' => $videoId,
            'genre_id' => $request->genre_id,
            'music_track_id' => $request->music_track_id,
            'description' => $request->description,
        ]);

        return redirect()->route('remixes.index')->with('message', '리믹스가 등록되었습니다!');
    }

    // 투표 로직 (기존 로직 유지 + 응답 방식 선택 가능)
    public function vote(Request $request, $id) 
    {
        $remix = Remix::findOrFail($id);
        $userId = auth()->id();

        $exists = DB::table('votes')
            ->where('user_id', $userId)
            ->where('remix_id', $id)
            ->exists();

        if ($exists) {
            return back()->with('error', '이미 투표하셨습니다.');
        }

        DB::transaction(function () use ($userId, $remix) {
            DB::table('votes')->insert([
                'user_id' => $userId,
                'remix_id' => $remix->id,
                'created_at' => now(),
            ]);
            $remix->increment('vote_count');
        });
            
        return back(); // Inertia는 페이지를 새로고침(back)하여 데이터를 갱신합니다.
    }
}