<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\CloudinaryService;
class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $post = new Post();
    $validated = $request->validate([
        'title'       => 'required|string|max:255',
        'content'     => 'required|string',
        'category_id' => 'required|exists:categories,id',
        'image'       => 'sometimes|image|max:4096', // 4MB max
    ]);

if ($request->hasFile('image')) {
    $upload = CloudinaryService::upload(
        $request->file('image'),
        'posts',
    );

       $post->featured_image_url = $upload['url'];
       $post->featured_image_public_id = $upload['public_id'];}
       $post->  title = $validated['title'];
       $post-> content     = $validated['content'];
       $post-> category_id = $validated['category_id'];
       $post-> user_id     = $request->user()->id;
    $post->save();
    return response()->json([
        'message' => 'Post created successfully',
        'post'    => $post,
    ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
   $validated = $request->validate([
        'category_id' => 'nullable|integer|min:0',
        'user_id'     => 'nullable|integer|min:0',
    ]);

    $query = Post::with(['category', 'author'])
        ->latest();

    if (!empty($validated['category_id']) && $validated['category_id'] !== 0) {
        $query->where('category_id', $validated['category_id']);
    }

    if (!empty($validated['user_id']) && $validated['user_id'] !== 0) {
        $query->where('user_id', $validated['user_id']);
    }

    $posts = $query->paginate(10);

    return response()->json($posts, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
