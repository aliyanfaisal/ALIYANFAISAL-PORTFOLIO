<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Services\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class OptimizeBlogImages extends Command
{
    protected $signature = 'blog:optimize-images
        {--dry-run : Report what would change without writing anything}
        {--delete-originals : Delete the old image file once the post points at the optimized one}';

    protected $description = 'Convert every blog post image to a 1600x900 WebP and record its dimensions (safe to re-run).';

    public function handle(ImageOptimizer $optimizer): int
    {
        $disk = Storage::disk('public');
        $dryRun = (bool) $this->option('dry-run');
        $converted = $skipped = $failed = 0;

        BlogPost::query()->whereNotNull('image_path')->orderBy('id')->each(function (BlogPost $post) use ($optimizer, $disk, $dryRun, &$converted, &$skipped, &$failed): void {
            $oldPath = $post->image_path;

            if (! $disk->exists($oldPath)) {
                $this->warn("#{$post->id} {$post->slug}: file missing ({$oldPath}), skipped.");
                $failed++;

                return;
            }

            if ($this->isOptimized($post)) {
                $skipped++;

                return;
            }

            try {
                $result = $optimizer->optimize($disk->get($oldPath));
            } catch (Throwable $exception) {
                $this->error("#{$post->id} {$post->slug}: {$exception->getMessage()}");
                $failed++;

                return;
            }

            $newPath = "blog/{$post->slug}.webp";
            $this->line(sprintf('#%d %s: %s (%s KB) -> %s (%dx%d, %s KB)', $post->id, $post->slug, $oldPath,
                number_format($disk->size($oldPath) / 1024), $newPath, $result['width'], $result['height'],
                number_format(strlen($result['contents']) / 1024)));

            if ($dryRun) {
                $converted++;

                return;
            }

            $disk->put($newPath, $result['contents']);

            // Image changes must not bump updated_at (sitemap lastmod / dateModified) or ping Google.
            BlogPost::withoutTimestamps(fn () => $post->forceFill([
                'image_path' => $newPath,
                'image_width' => $result['width'],
                'image_height' => $result['height'],
            ])->saveQuietly());

            if ($this->option('delete-originals') && $oldPath !== $newPath
                && ! BlogPost::where('image_path', $oldPath)->exists()) {
                $disk->delete($oldPath);
            }

            $converted++;
        });

        $this->info(($dryRun ? '[dry run] ' : '')."Converted: {$converted}, already optimized: {$skipped}, failed: {$failed}.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function isOptimized(BlogPost $post): bool
    {
        return str_ends_with($post->image_path, '.webp')
            && $post->image_width !== null
            && $post->image_height !== null
            && $post->image_width <= ImageOptimizer::WIDTH
            && $post->image_height <= ImageOptimizer::HEIGHT;
    }
}
