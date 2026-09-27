<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\PostReaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostReactionController extends Controller
{
    public function store(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'emoji' => ['required', Rule::in(PostReaction::EMOJIS)],
        ]);

        $sessionId = $request->session()->getId();

        $existing = $post->reactions()->where('session_id', $sessionId)->first();

        if ($existing && $existing->emoji === $data['emoji']) {
            $existing->delete();
        } elseif ($existing) {
            $existing->update(['emoji' => $data['emoji']]);
        } else {
            $post->reactions()->create([
                'session_id' => $sessionId,
                'emoji' => $data['emoji'],
            ]);
        }

        if ($request->wantsJson()) {
            $counts = $post->reactions()->selectRaw('emoji, count(*) as count')->groupBy('emoji')->pluck('count', 'emoji');

            return response()->json([
                // Cast to an object so an empty result serializes as {} rather than [],
                // which would otherwise corrupt the shape the frontend expects back.
                'counts' => (object) $counts->all(),
                'mine' => $post->reactions()->where('session_id', $sessionId)->value('emoji'),
            ]);
        }

        return redirect(route('blog.show', $post));
    }
}
