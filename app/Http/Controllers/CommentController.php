<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $validated = $request->validate([
        'commentable_type' => 'required|string',
        'commentable_id' => 'required|integer',
    ]);

    $comments = Comment::where('commentable_type', $validated['commentable_type'])
        ->where('commentable_id', $validated['commentable_id'])
        ->whereNull('parent_id')
        ->with([
            'user:id,name',

        ])
        ->latest()
        ->get();

    return response()->json([
        'comments' => $comments,
    ]);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'body' => 'required|string',
        'commentable_id' => 'required|integer',
        'commentable_type' => 'required|string',
        'parent_id' => 'nullable|exists:comments,id',
    ]);

    $comment = Comment::create([
        'user_id' => $request->user()->id,
        'body' => $validated['body'],
        'commentable_id' => $validated['commentable_id'],
        'commentable_type' => $validated['commentable_type'],
        'parent_id' => $validated['parent_id'] ?? null,
    ]);

    return response()->json([
        'message' => 'Comment added successfully',
        'comment' => $comment->load('user'),
    ], 201);
}
public function replies(Comment $comment)
{
    $replies = $comment->replies()
        ->with('user:id,name')
        ->latest()
        ->get();

    return response()->json([
        'replies' => $replies,
    ]);
}

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, Comment $comment)
{

    if ($comment->user_id !== $request->user()->id) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    $validated = $request->validate([
        'body' => 'required|string',
    ]);

    $comment->update([
        'body' => $validated['body'],
    ]);

    return response()->json([
        'message' => 'Comment updated successfully',
        'comment' => $comment,
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Request $request, Comment $comment)
{
    $user = $request->user();

    // Get the post owner (works for polymorphic)
    $postOwnerId = null;
    if ($comment->commentable && property_exists($comment->commentable, 'id')) {
        $postOwnerId = $comment->commentable->user_id;
    }

    $isAdmin = $user->role === 'admin';
    $isCommentOwner = $comment->user_id === $user->id;
    $isPostOwner = $postOwnerId === $user->id;

    if (!($isAdmin || $isCommentOwner || $isPostOwner)) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    $comment->delete();

    return response()->json([
        'message' => 'Comment deleted successfully'
    ]);
}
}
