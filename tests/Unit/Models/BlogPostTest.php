<?php

namespace Tests\Unit\Models;

use App\Models\BlogPost;
use Tests\TestCase;

class BlogPostTest extends TestCase
{
    public function test_body_html_converts_markdown_to_html(): void
    {
        $post = new BlogPost([
            'body' => "## Heading\n\n```php\necho 'hi';\n```\n",
        ]);

        $html = $post->body_html;

        $this->assertStringContainsString('<h2 id="heading">Heading</h2>', $html);
        $this->assertStringContainsString('<pre><code class="language-php">', $html);
    }

    public function test_body_html_strips_raw_html_from_the_source(): void
    {
        $post = new BlogPost([
            'body' => '<script>alert(1)</script>Some text.',
        ]);

        $this->assertStringNotContainsString('<script>', $post->body_html);
    }
}
