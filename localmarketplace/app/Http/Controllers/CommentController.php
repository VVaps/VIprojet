<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created comment or reply.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'product_id' => 'required|exists:products,id',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        // Load relationships for response
        $comment->load('user', 'replies');

        return response()->json([
            'success' => true,
            'message' => 'Comment posted successfully!',
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'name' => $comment->user->name,
                ],
                'created_at' => $comment->created_at->diffForHumans(),
                'replies' => []
            ]
        ]);
    }

    /**
     * Fetch all comments and nested replies for a product.
     */
    public function fetch(Product $product)
    {
        $comments = $product->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->latest()
            ->get();

        $formattedComments = $comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'name' => $comment->user->name,
                ],
                'created_at' => $comment->created_at->diffForHumans(),
                'replies' => $comment->replies->map(function ($reply) {
                    return [
                        'id' => $reply->id,
                        'content' => $reply->content,
                        'user' => [
                            'name' => $reply->user->name,
                        ],
                        'created_at' => $reply->created_at->diffForHumans(),
                    ];
                })->toArray()
            ];
        });

        return response()->json([
            'success' => true,
            'comments' => $formattedComments
        ]);
    }
}
