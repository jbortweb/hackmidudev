<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Project;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CommentController extends Controller
{
    public function store(Request $request, string $projectId)
    {
        $user = $request->user();

        $lastComment = Comment::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastComment && $lastComment->created_at->diffInSeconds(Carbon::now()) < 30) {
            return response()->json([
                'message' => 'Debes esperar 30 segundos entre comentarios.'
            ], 429);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = Comment::create([
            'user_id' => $user->id,
            'project_id' => $projectId,
            'content' => $validated['content'],
        ]);

        Project::where('id', $projectId)->increment('comments_count');

        $comment->load('user');

        return response()->json($comment, 201);
    }
}
