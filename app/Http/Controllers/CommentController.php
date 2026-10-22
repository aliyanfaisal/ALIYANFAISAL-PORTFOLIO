<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\Setting;
use App\Rules\SafeComment;
use App\Services\TurnstileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, string $slug, TurnstileService $turnstile): RedirectResponse
    {
        $post = $this->publishedPost($slug);

        $data = $this->validateComment($request);

        if (! $this->verifyTurnstile($request, $turnstile)) {
            return $this->turnstileFailedResponse();
        }

        $post->comments()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'body' => $data['body'],
            'status' => Comment::initialStatus(),
            'session_id' => $request->session()->getId(),
        ]);

        return redirect(route('blog.show', $post).'#comments')
            ->with('status', $this->postedMessage('comment'));
    }

    public function reply(Request $request, string $slug, Comment $comment, TurnstileService $turnstile): RedirectResponse
    {
        $post = $this->publishedPost($slug);

        abort_unless($comment->blog_post_id === $post->id && $comment->isApproved(), 404);

        // Replies always attach to the top-level comment, collapsing deeper threads to one level.
        $root = $comment->parent_id ? $comment->parent : $comment;
        $errorBag = "comment-reply-{$root->id}";

        $data = $this->validateComment($request, $errorBag);

        if (! $this->verifyTurnstile($request, $turnstile)) {
            return $this->turnstileFailedResponse($errorBag);
        }

        $post->comments()->create([
            'parent_id' => $root->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'body' => $data['body'],
            'status' => Comment::initialStatus(),
            'session_id' => $request->session()->getId(),
        ]);

        return redirect(route('blog.show', $post)."#comment-{$root->id}")
            ->with('status', $this->postedMessage('reply'));
    }

    public function update(Request $request, string $slug, Comment $comment): RedirectResponse
    {
        $post = $this->publishedPost($slug);

        abort_unless($comment->blog_post_id === $post->id, 404);
        abort_unless($comment->isOwnedBySession($request->session()->getId()), 403);

        $data = $request->validateWithBag("comment-edit-{$comment->id}", [
            'name' => ['required', 'string', 'max:100', new SafeComment],
            'body' => ['required', 'string', 'max:2000', new SafeComment],
        ]);

        $comment->update($data + ['status' => Comment::initialStatus()]);

        return redirect(route('blog.show', $post)."#comment-{$comment->id}")
            ->with('status', 'Your comment has been updated.');
    }

    public function destroy(Request $request, string $slug, Comment $comment): RedirectResponse
    {
        $post = $this->publishedPost($slug);

        abort_unless($comment->blog_post_id === $post->id, 404);
        abort_unless($comment->isOwnedBySession($request->session()->getId()), 403);

        $comment->delete();

        return redirect(route('blog.show', $post).'#comments')
            ->with('status', 'Your comment has been deleted.');
    }

    private function postedMessage(string $noun): string
    {
        return Setting::current()->auto_approve_comments
            ? "Thanks — your {$noun} has been posted."
            : "Thanks — your {$noun} was received and will appear once it has been approved.";
    }

    private function publishedPost(string $slug): BlogPost
    {
        return BlogPost::published()->where('slug', $slug)->firstOrFail();
    }

    /**
     * @return array{name: string, email: string, body: string}
     */
    private function validateComment(Request $request, ?string $errorBag = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:100', new SafeComment],
            'email' => ['required', 'email', 'max:150'],
            'body' => ['required', 'string', 'max:2000', new SafeComment],
            'cf-turnstile-response' => ['required', 'string'],
        ];

        return $errorBag ? $request->validateWithBag($errorBag, $rules) : $request->validate($rules);
    }

    private function verifyTurnstile(Request $request, TurnstileService $turnstile): bool
    {
        return $turnstile->verify((string) $request->input('cf-turnstile-response'), $request->ip());
    }

    private function turnstileFailedResponse(?string $errorBag = null): RedirectResponse
    {
        $errors = ['cf-turnstile-response' => 'Verification failed. Please try again.'];

        return $errorBag
            ? back()->withErrors($errors, $errorBag)->withInput()
            : back()->withErrors($errors)->withInput();
    }
}
