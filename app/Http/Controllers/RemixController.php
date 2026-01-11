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
        $query = Remix::with(['genre', 'user']);

        // 특정 곡 필터링 수정
        if ($request->filled('track')) {
            // '제목|가수' 형태를 다시 나눕니다.
            $parts = explode('|', $request->track);
            
            if (count($parts) === 2) {
                $query->where('track_title', $parts[0])
                    ->where('artist_name', $parts[1]);
            }
        }

        // 2. 장르 필터링
        if ($request->genre) {
            $query->whereHas('genre', function($q) use ($request) {
                $q->where('slug', $request->genre);
            });
        }

        // 3. 정렬 값 가져오기 (기본값 'latest' 설정)
        $sort = $request->input('sort', 'popular');

        // 4. 정렬 로직 적용
        if ($sort === 'popular') {
            $query->orderBy('vote_count', 'desc');
        } else {
            $query->latest();
        }

        return Inertia::render('Remixes/Index', [
            'remixes' => $query->paginate(12)->withQueryString(),
            'remixes_all' => Remix::select('track_title', 'artist_name', 'album_cover_url')
                               ->groupBy('track_title', 'artist_name', 'album_cover_url')
                               ->get(),
            'genres' => Genre::has('remixes')->get(),
            // 4. filters 데이터에 기본값을 명시적으로 포함
            'filters' => [
                'genre' => $request->genre ?? '',
                'sort' => $sort, // 값이 없더라도 'latest'가 전달됨
            ]
        ]);
    }

    // 업로드 페이지 보여주기
    public function create()
    {
        return Inertia::render('Remixes/Create', [
            'genres' => Genre::all(),
            // 'musicTracks' => MusicTrack::all(),
        ]);
    }

    // 저장 로직 (마이그레이션 구조 반영)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'youtube_url' => 'required|url',
            'genre_id' => 'required|exists:genres,id',
            // iTunes에서 넘어온 정보들 검증
            'track_title' => 'required|string|max:255',
            'artist_name' => 'required|string|max:255',
            'album_cover_url' => 'nullable|string',
            'itunes_track_id' => 'nullable|string',
        ]);

        // 유튜브 비디오 ID 추출 (간단한 정규식 버전)
        preg_match("/(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be\/)[^&\n]+/", $request->youtube_url, $matches);
        $videoId = $matches[0] ?? null;

        Remix::create([
            // 'user_id' => auth()->id(),
            // 'title' => $request->title,
            // 'youtube_url' => $request->youtube_url,
            // 'youtube_video_id' => $videoId,
            // 'genre_id' => $request->genre_id,
            // 'music_track_id' => '1',
            // 'description' => $request->description,

            'user_id' => auth()->id(),
            'genre_id' => $validated['genre_id'],
            'title' => $validated['title'],
            'description' => $request->description,
            'youtube_url' => $validated['youtube_url'],
            'youtube_video_id' => $videoId,
            
            // --- 새롭게 추가된 원곡 정보들 ---
            'track_title' => $validated['track_title'],
            'artist_name' => $validated['artist_name'],
            'album_cover_url' => $validated['album_cover_url'],
            'itunes_track_id' => $validated['itunes_track_id'],
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