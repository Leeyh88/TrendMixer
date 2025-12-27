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
    
    public function vote($id) 
    {
        $remix = \App\Models\Remix::findOrFail($id);
        $remix->increment('vote_count');

        return response()->json(['success' => true, 'new_count' => $remix->vote_count]);
    }
}
