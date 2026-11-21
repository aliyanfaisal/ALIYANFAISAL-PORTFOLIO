<?php

namespace Tests\Feature\Api;

use App\Models\LinkedInToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Tests\TestCase;

class LinkedInPostControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.linkedin.api_token' => 'test-token']);
        Sleep::fake();

        LinkedInToken::store([
            'access_token' => 'access',
            'refresh_token' => 'refresh',
            'expires_at' => now()->addDay(),
            'member_urn' => 'urn:li:person:abc',
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function fakeLinkedIn(array $overrides = []): void
    {
        Http::fake($overrides + [
            'example.com/*' => Http::response('bytes', 200, ['Content-Type' => 'image/png']),
            'api.linkedin.com/rest/images?action=initializeUpload' => Http::response([
                'value' => ['uploadUrl' => 'https://upload.linkedin.test/put', 'image' => 'urn:li:image:img1'],
            ]),
            'upload.linkedin.test/*' => Http::response('', 201),
            'api.linkedin.com/rest/images/*' => Http::response(['status' => 'AVAILABLE']),
            'api.linkedin.com/rest/posts/*' => Http::response(['lifecycleState' => 'PUBLISHED', 'visibility' => 'PUBLIC']),
            'api.linkedin.com/rest/posts' => Http::response('', 201, ['x-restli-id' => 'urn:li:share:123']),
        ]);
    }

    private function postPayload(): array
    {
        return ['image_url' => 'https://example.com/a.png', 'caption' => 'Hello'];
    }

    public function test_it_publishes_a_public_post_with_the_published_lifecycle_state(): void
    {
        $this->fakeLinkedIn();

        $this->postJson('/api/linkedin-post', $this->postPayload(), ['Authorization' => 'Bearer test-token'])
            ->assertCreated()
            ->assertJson(['post_urn' => 'urn:li:share:123']);

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && $request->url() === 'https://api.linkedin.com/rest/posts'
            && $request['lifecycleState'] === 'PUBLISHED'
            && $request['visibility'] === 'PUBLIC'
            && $request['content']['media']['id'] === 'urn:li:image:img1');
    }

    public function test_it_waits_for_the_image_to_finish_processing_before_posting(): void
    {
        $this->fakeLinkedIn([
            'api.linkedin.com/rest/images/*' => Http::sequence()
                ->push(['status' => 'PROCESSING'])
                ->push(['status' => 'AVAILABLE']),
        ]);

        $this->postJson('/api/linkedin-post', $this->postPayload(), ['Authorization' => 'Bearer test-token'])
            ->assertCreated();

        Http::assertSentCount(7);
        Sleep::assertSleptTimes(1);
    }

    public function test_it_fails_when_linkedin_cannot_process_the_image(): void
    {
        $this->fakeLinkedIn([
            'api.linkedin.com/rest/images/*' => Http::response(['status' => 'PROCESSING_FAILED']),
        ]);

        $this->postJson('/api/linkedin-post', $this->postPayload(), ['Authorization' => 'Bearer test-token'])
            ->assertStatus(500);

        Http::assertNotSent(fn (Request $request) => $request->url() === 'https://api.linkedin.com/rest/posts');
    }

    public function test_it_fails_when_the_created_post_is_not_published(): void
    {
        $this->fakeLinkedIn([
            'api.linkedin.com/rest/posts/*' => Http::response(['lifecycleState' => 'DRAFT']),
        ]);

        $this->postJson('/api/linkedin-post', $this->postPayload(), ['Authorization' => 'Bearer test-token'])
            ->assertStatus(500);
    }

    public function test_it_tolerates_a_post_that_cannot_be_read_back(): void
    {
        $this->fakeLinkedIn([
            'api.linkedin.com/rest/posts/*' => Http::response(['message' => 'Not enough permissions'], 403),
        ]);

        $this->postJson('/api/linkedin-post', $this->postPayload(), ['Authorization' => 'Bearer test-token'])
            ->assertCreated();
    }

    public function test_it_downscales_and_reencodes_oversized_images_before_uploading(): void
    {
        $image = imagecreatetruecolor(3000, 2000);
        ob_start();
        imagepng($image);
        $png = (string) ob_get_clean();

        $this->fakeLinkedIn([
            'example.com/*' => Http::response($png, 200, ['Content-Type' => 'image/png']),
        ]);

        $this->postJson('/api/linkedin-post', $this->postPayload(), ['Authorization' => 'Bearer test-token'])
            ->assertCreated();

        Http::assertSent(function (Request $request) {
            if ($request->url() !== 'https://upload.linkedin.test/put') {
                return false;
            }

            $info = getimagesizefromstring($request->body());

            return $info['mime'] === 'image/jpeg' && max($info[0], $info[1]) === 2048;
        });
    }
}
