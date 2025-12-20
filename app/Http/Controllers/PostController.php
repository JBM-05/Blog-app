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
        'image'       => 'sometimes|required|image|max:4096',
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
$user = $request->user();
    if ($post->user_id !== $user->id ) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $validatedData = $request->validate([
        'title'       => 'sometimes|required|string|max:255',
        'content'     => 'sometimes|required|string',
        'category_id' => 'sometimes|required|exists:categories,id',
        'image'       => 'sometimes|required|image|max:4096',
    ]);

    if (isset($validatedData['title'])) {
        $post->title = $validatedData['title'];
    }

    if (isset($validatedData['content'])) {
        $post->content = $validatedData['content'];
    }

    if (isset($validatedData['category_id'])) {
        $post->category_id = $validatedData['category_id'];
    }

  if (isset($validatedData['image'])) {


       if ($post->featured_image_public_id) {
    CloudinaryService::delete($post->featured_image_public_id);
}
    $upload = CloudinaryService::upload(
        $validatedData['image'],
        'posts'
    );

    $post->featured_image_url = $upload['url'];
    $post->featured_image_public_id = $upload['public_id'];
}

    $post->save();

    return response()->json([
        'message' => 'Post updated successfully',
        'post' => $post,
    ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Post $post)
    {
        $user = $request->user();
        if( $post->user_id !== $user->user_id && $user->role !== 'admin'){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
         CloudinaryService::delete($post->featured_image_public_id);
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}
