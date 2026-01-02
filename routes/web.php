<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Remix;

// 메인 페이지
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// 로그인한 사용자만 접근 가능한 메뉴들
Route::middleware(['auth'])->group(function () {
    // 1. 순위 페이지
    Route::get('/rankings', function (Request $request) {
        // 필터 값 받기(기본 : 좋아요)
        $sort = $request->query('sort','votes');
        $column = ($sort === 'views') ? 'view_count' : 'vote_count';

        $rankings = Remix::with(['user', 'genre', 'musicTrack'])
            ->orderBy($column, 'desc')
            ->take(10)
            ->get();

        return Inertia::render('Rankings/Index', [
            'rankings' => $rankings,
            'currentSort' => $sort
        ]);
    })->name('rankings');

    // 2. 월드컵 페이지
    Route::get('/worldcup', function () {
        // 무작위로 2곡
        $candidates = Remix::with(['musicTrack', 'genre'])
            ->inRandomOrder()
            ->take(2)
            ->get();

        return Inertia::render('Worldcup/Index', [
            'candidates' => $candidates
        ]);
    })->name('worldcup');

    // 3. 게시판
    Route::get('/board', function () {
        return Inertia::render('Board/Index');
    })->name('board');

    // 4. 핫 트렌드 (외부 API 연동 예정)
    Route::get('/trends', function () {
        return Inertia::render('Trends/Index');
    })->name('trends');

    // 투표(선택) 처리 로직
    Route::post('/worldcup/{id}/vote', function ($id) {
        // 1. 선택된 곡을 DB에서 찾습니다.
        $remix = Remix::findOrFail($id);

        // 2. 투표수(vote_count)를 1 올립니다.
        $remix->increment('vote_count');

        return back(); 
    })->name('worldcup.vote');

    // 대시보드
    Route::get('/dashboard', function() {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // 프로필
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
