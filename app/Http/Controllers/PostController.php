<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * 게시판 목록 조회
     */
    public function index(Request $request)
    {
        // 1. 요청받은 카테고리, 검색어 값 가져오기
        $category = $request->query('category');
        $search = $request->query('search');

        return Inertia::render('Posts/Index', [
            'posts' => Post::with('user')
                // 댓글 수와 좋아요 수를 쿼리에 포함
                ->withCount(['comments', 'likes']) 
                // 2. 카테고리가 있으면 해당 카테고리만 필터링
                ->when($category, function ($query, $category) {
                    return $query->where('category', $category);
                })
                // 3. 검색어 필터
                ->when($search, function ($query, $search) {
                    return $query->where(function($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'filters' => [
                'category' => $category,
                'search' => $search
            ]
        ]);
    }

    /**
     * 글 쓰기 페이지
     */
    public function create()
    {
        return Inertia::render('Posts/Create');
    }

    // 상세보기
    public function show(Post $post)
    {
        // 조회수 추가
        $post->increment('view_count');

        $post->loadCount(['likes','comments']);

        // 게시글 랜더링
        return Inertia::render('Posts/Show', [
            'post' => $post->load([
                'user', 
                'comments' => function($query) {
                    $query->whereNull('parent_id') // 부모가 없는(최상위) 댓글만 1차로 가져옴
                        ->latest()               // 최신순 정렬
                        ->with(['user', 'replies.user']); // 댓글 작성자 + 대댓글 + 대댓글 작성자 로드
                }
            ])
        ]);
    }

    public function store(Request $request)
    {
        // 1. 데이터 검증 (필수 입력값 확인)
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
        ]);

        // 2. 현재 로그인한 유저의 ID와 함께 저장
        $request->user()->posts()->create($validated);

        // 3. 목록으로 돌아가기
        return redirect()->route('posts.index')->with('message', '게시글이 성공적으로 등록되었습니다.');
    }

    // 수정하기
    public function edit(Post $post)
    {
        // 본인글인지 체크
        if (auth()->id() !== $post->user_id)
        {
            abort(403, '수정 권한이 없습니다.');
        }

        return Inertia::render('Posts/Edit', [
            'post' => $post
        ]);
    }

    public function update(Request $request, Post $post)
    {
        // 본인 확인 
        if (auth()->id() !== $post->user_id)
        {
            abort(403, '수정 권한이 없습니다.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
        ]);

        $post->update($validated);

        return redirect()->route('posts.show', $post->id)->with('message', '글이 수정되었습니다.');
    }
}