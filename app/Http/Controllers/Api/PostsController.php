<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Post;
use Illuminate\Http\Request;
use DB;

class PostsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userIds = $user->friends()->pluck('id');
        $userIds[] = $user->id;

        $response = Post::whereIn('user_id', $userIds)->latest()->get();
        $response = $response->map(function ($value){
            $response = $value;
            $response->imageUrl = null;
            if($value->image) {
                $response->imageUrl = asset("image/" . $value->image);
            }

            return $response;
        });
        return $response;
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $imageName = "";
        if($request->image) {
            $file = $request->file('image');
            $imageName = date('YmdHis');
            $imageName .= $file->getClientOriginalName();
            $file->move(
                public_path('image'),
                $imageName
            );
        } 
        $newPost = new Post([
            'content' => $request->get('content'),
            'image' => $imageName
        ]
        );
        $post = $user->posts()->save($newPost);
        $post->load('user');

        return response()->json($post->toArray(), 201);
    }

    public function storeComment(Request $request, $id) {
        $content = $request->content;
        $post_id = $id;
        $created_by = auth()->id();
        try {
            DB::table('comments')
            ->insert([
                "content" => $content,
                "post_id" => $post_id,
                "created_by" => $created_by,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s')
            ]);
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json(['message' => "success"]);
    }
}
