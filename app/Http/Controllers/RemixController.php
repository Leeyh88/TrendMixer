<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Remix;

class RemixController extends Controller
{
    public function index() 
    {
        // with()를 사용하면 관계된 데이터를 한 번에 가져와서 효율적입니다.
        $remixes = Remix::with(['genre', 'musicTrack'])->get();
        
        return response()->json($remixes);
    }
    
    public function vote(Request $request, $id) 
    {
        // 로그인체크
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => '로그인이 필요합니다.'], 401);
        }

        $user = auth()->user();
        $remix = Remix::findOrFail($id);

        // 투표했는지 체크
        $exists = \DB::table('votes')
            ->where('user_id', $user->id)
            ->where('remix_id', $id)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => '이미 투표하셨습니다.'], 400);
        }

        // 투표 기록 및 카운트 증가 
        \DB::transaction(function () use ($user, $remix) {
            \DB::table('votes')->insert([
                'user_id' => $user->id,
                'remix_id' => $remix->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $remix->increment('vote_count');
        });
            
        return response()->json(['success' => true, 'new_count' => $remix->fresh()->vote_count]);
    }
}
