<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('blog:publish-scheduled')]
#[Description('Publish draft blog posts whose scheduled publish time has arrived')]
class PublishScheduledBlogPosts extends Command
{
    public function handle(): int
    {
        $count = 0;

        BlogPost::query()
            ->where('status', 'draft')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->each(function (BlogPost $post) use (&$count): void {
                $post->status = 'published';
                $post->save();
                $count++;
            });

        $this->info("Published {$count} scheduled post(s).");

        return self::SUCCESS;
    }
}
