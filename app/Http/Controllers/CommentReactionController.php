<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\CommentReaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommentReactionController extends Controller
{
    public function store(Request $request, string $slug, Comment $comment): JsonResponse|RedirectResponse
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();

        abort_unless($comment->blog_post_id === $post->id && $comment->isApproved(), 404);

        $data = $request->validate([
            'emoji' => ['required', Rule::in(CommentReaction::EMOJIS)],
        ]);

        $sessionId = $request->session()->getId();

        $existing = $comment->reactions()->where('session_id', $sessionId)->first();

        if ($existing && $existing->emoji === $data['emoji']) {
            $existing->delete();
        } elseif ($existing) {
            $existing->update(['emoji' => $data['emoji']]);
        } else {
            $comment->reactions()->create([
                'session_id' => $sessionId,
                'emoji' => $data['emoji'],
            ]);
        }

        if ($request->wantsJson()) {
            $counts = $comment->reactions()->selectRaw('emoji, count(*) as count')->groupBy('emoji')->pluck('count', 'emoji');

            return response()->json([
                // Cast to an object so an empty result serializes as {} rather than [],
                // which would otherwise corrupt the shape the frontend expects back.
                'counts' => (object) $counts->all(),
                'mine' => $comment->reactions()->where('session_id', $sessionId)->value('emoji'),
            ]);
        }

        return redirect(route('blog.show', $post)."#comment-{$comment->id}");
    }
}
