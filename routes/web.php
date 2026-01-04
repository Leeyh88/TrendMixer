<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PostLikeController;
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

    // 3. 게시판 전체 기능 (index, create, store, show 등) 자동 매핑
    Route::resource('posts', PostController::class);
    // 3-2 게시판 댓글 작성
    Route::post('posts/{post}/comments', [CommentController::class, 'store'])->name('posts.comments.store');
    // 3-3 게시판 댓글 삭제
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    // 3-4 게시판 이미지 업로드
    Route::post('/upload-image', [ImageController::class, 'store'])->name('image.upload');
    // 3-5 게시판 좋아요
    Route::post('/posts/{post}/like', [PostLikeController::class, 'store'])->name('posts.like')->middleware('auth');

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
