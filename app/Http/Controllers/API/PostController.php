<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Auth::user()->posts;
        if ($posts->isEmpty()) {
            return response()->json(['message' => 'No records found'], 404);
        }
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Auth::user()->posts()->create($request->all());

        if ($post) {
            return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
        } else {
            return response()->json(['message' => 'Failed to create post'], 500);
        }
    }

    public function show(Post $post)
    {
        $this->authorize('view', $post);
        
        if ($post) {
            return response()->json($post);
        } else {
            return response()->json(['message' => 'Post not found'], 404);
        }
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'title' => 'string|max:255',
            'content' => 'string',
        ]);

        $updated = $post->update($request->all());

        if ($updated) {
            return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
        } else {
            return response()->json(['message' => 'Failed to update post'], 500);
        }
    }

    public function destroy(Post $post)
    {
        if (is_null($post)) {
            return response()->json(['message' => 'Post Not Found'], 404);
        }
        $this->authorize('delete', $post);

        $deleted = $post->delete();

        if ($deleted) {
            return response()->json(['message' => 'Post deleted successfully']);
        } else {
            return response()->json(['message' => 'Failed to delete post'], 500);
        }
    }
}
