<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RemixController;
use Illuminate\Support\Facades\DB; // 👈 이 줄을 추가해야 합니다!

Route::get('/remixes', [RemixController::class, 'index']);

Route::get('/test', function () {
    try {
        // DB 연결 테스트 
        $firstGenre = DB::table('genres')->first();

        return response()->json([
            'message' => 'Database connection is solid!',
            'data' => $firstGenre,
            'status' => 'success'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'DB Connection Failed',
            'error' => $e->getMessage()
        ], 500);
    }
});
