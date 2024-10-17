<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller implements HasMiddleware
{
    //define middleware to protect necessary routes.
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    
    //Display a listing of the resource.
    public function index()
    {
        $post = Post::all();

        return response()->json([
            'status' => true,
            'data' => $post
        ]);
    }

    
    //Store a newly created resource in storage. 
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|min:3',
            'author' => 'required|min:3',
            'body' => 'required'
        ]);

        $post = $request->user()->posts()->create($fields); //creating a post via the user.
        // $post = Post::create($fields); //creating a post with the model.

        return response()->json([
            'status' => true,
            'data' => $post
        ]);
    }

    
    //Display the specified resource. 
    public function show(Post $post)
    {
        $post = Post::get();

        return response()->json([
            'status' => true,
            'data' => $post
        ]);
    }

    
    //Update the specified resource in storage. 
    public function update(Request $request, Post $post)
    {
        Gate::authorize('modify', $post); //implementing the modify function in the post policy.

        $fields = $request->validate([
            'title' => 'required|min:3',
            'author' => 'required|min:3',
            'body' => 'required'
        ]);

        $post->update($fields);

        return response()->json([
            'status' => true,
            'data' => $post
        ]);

    }

    
    //Remove the specified resource from storage. 
    public function destroy(Post $post)
    {
        Gate::authorize('modify', $post); //implementing the modify function in the post policy.
        
        $post->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
