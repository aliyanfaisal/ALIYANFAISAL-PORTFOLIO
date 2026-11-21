<?php

namespace App\Services;

use App\Exceptions\LinkedInApiException;
use App\Exceptions\LinkedInNotConnectedException;
use App\Models\LinkedInToken;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;

class LinkedInService
{
    private const API_VERSION = '202609';

    private const IMAGE_STATUS_POLL_ATTEMPTS = 10;

    private const MAX_IMAGE_BYTES = 4_000_000;

    private const MAX_IMAGE_DIMENSION = 2048;

    /**
     * Exchange an OAuth authorization code for an access token.
     *
     * @return array{access_token: string, expires_in: int, refresh_token: ?string, refresh_token_expires_in: ?int}
     */
    public function exchangeAuthorizationCode(string $code): array
    {
        $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('services.linkedin.redirect_uri'),
            'client_id' => config('services.linkedin.client_id'),
            'client_secret' => config('services.linkedin.client_secret'),
        ]);

        $this->assertSuccessful($response, 'Failed to exchange authorization code for an access token.');

        return $response->json();
    }

    /**
     * Fetch the LinkedIn member URN (urn:li:person:{sub}) for the given access token.
     */
    public function fetchMemberUrn(string $accessToken): string
    {
        $response = Http::withToken($accessToken)->get('https://api.linkedin.com/v2/userinfo');

        $this->assertSuccessful($response, 'Failed to fetch LinkedIn member info.');

        return 'urn:li:person:'.$response->json('sub');
    }

    /**
     * Return a currently-valid access token, refreshing it first if it's expired or expiring soon.
     */
    public function getValidAccessToken(): string
    {
        $token = LinkedInToken::current();

        if (! $token) {
            throw new LinkedInNotConnectedException;
        }

        if ($token->expires_at->subMinutes(5)->isFuture()) {
            return $token->access_token;
        }

        if (blank($token->refresh_token)) {
            Log::error('LinkedIn access token expired and no refresh token is stored; re-run the OAuth connect flow.', [
                'expires_at' => $token->expires_at->toIso8601String(),
            ]);

            throw new LinkedInApiException(
                'LinkedIn access token expired and no refresh token is stored. Reconnect via /auth/linkedin.',
                401,
                'missing_refresh_token',
            );
        }

        $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $token->refresh_token,
            'client_id' => config('services.linkedin.client_id'),
            'client_secret' => config('services.linkedin.client_secret'),
        ]);

        if ($response->failed()) {
            Log::error('LinkedIn token refresh failed', [
                'status' => $response->status(),
                'response' => $response->json() ?? $response->body(),
                'expires_at' => $token->expires_at->toIso8601String(),
                'token_updated_at' => $token->updated_at?->toIso8601String(),
            ]);
        }

        $this->assertSuccessful($response, 'Failed to refresh LinkedIn access token.');

        $data = $response->json();

        $token = LinkedInToken::store([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? $token->refresh_token,
            'expires_at' => now()->addSeconds($data['expires_in']),
        ]);

        return $token->access_token;
    }

    /**
     * Download the image, upload it to LinkedIn, and publish it as a native image post.
     * Returns the created post's URN and its human-viewable feed URL.
     *
     * @return array{urn: string, url: string}
     */
    public function publishImagePost(string $imageUrl, string $caption): array
    {
        $accessToken = $this->getValidAccessToken();
        $memberUrn = LinkedInToken::current()->member_urn;

        $imageResponse = Http::timeout(15)->get($imageUrl);

        if ($imageResponse->failed()) {
            throw new LinkedInApiException(
                "Failed to download image from {$imageUrl}.",
                $imageResponse->status(),
                $imageResponse->body(),
            );
        }

        $contentType = strtolower(explode(';', $imageResponse->header('Content-Type') ?? 'image/jpeg')[0]);

        [$imageBytes, $contentType] = $this->prepareImage($imageResponse->body(), $contentType);

        $imageUrn = $this->registerAndUploadImage($accessToken, $memberUrn, $imageBytes, $contentType);

        $postUrn = $this->createPost($accessToken, $memberUrn, $imageUrn, $caption);

        return [
            'urn' => $postUrn,
            'url' => "https://www.linkedin.com/feed/update/{$postUrn}/",
        ];
    }

    /**
     * Make sure the image is something LinkedIn will accept. LinkedIn returns a 201 on upload but then
     * marks oversized or unsupported images PROCESSING_FAILED, so anything over the size budget, over
     * the dimension budget, or not a JPEG/PNG is downscaled and re-encoded as JPEG. If the image can't
     * be decoded it is sent unchanged.
     *
     * @return array{0: string, 1: string} The image bytes and their content type.
     */
    private function prepareImage(string $bytes, string $contentType): array
    {
        $info = @getimagesizefromstring($bytes);

        Log::info('LinkedIn source image', [
            'bytes' => strlen($bytes),
            'declared_content_type' => $contentType,
            'detected_mime' => $info['mime'] ?? null,
            'width' => $info[0] ?? null,
            'height' => $info[1] ?? null,
        ]);

        if ($info === false || ! function_exists('imagecreatefromstring')) {
            return [$bytes, $contentType];
        }

        [$width, $height] = $info;
        $mime = $info['mime'];
        $isSupportedType = in_array($mime, ['image/jpeg', 'image/png'], true);
        $needsResize = max($width, $height) > self::MAX_IMAGE_DIMENSION;

        if ($isSupportedType && ! $needsResize && strlen($bytes) <= self::MAX_IMAGE_BYTES) {
            return [$bytes, $mime];
        }

        $source = @imagecreatefromstring($bytes);

        if ($source === false) {
            return [$bytes, $contentType];
        }

        if ($needsResize) {
            $scale = self::MAX_IMAGE_DIMENSION / max($width, $height);
            $resized = imagescale($source, (int) round($width * $scale), (int) round($height * $scale));

            if ($resized !== false) {
                $source = $resized;
            }
        }

        ob_start();
        imagejpeg($source, null, 85);
        $jpeg = (string) ob_get_clean();

        Log::info('LinkedIn image re-encoded', [
            'from_bytes' => strlen($bytes),
            'to_bytes' => strlen($jpeg),
            'width' => imagesx($source),
            'height' => imagesy($source),
        ]);

        return [$jpeg, 'image/jpeg'];
    }

    /**
     * Register an image upload with LinkedIn and upload the raw bytes to the returned upload URL.
     * Returns the resulting image URN (e.g. urn:li:image:xxxx).
     */
    private function registerAndUploadImage(string $accessToken, string $memberUrn, string $imageBytes, string $contentType): string
    {
        $initResponse = Http::withHeaders($this->restHeaders($accessToken))
            ->post('https://api.linkedin.com/rest/images?action=initializeUpload', [
                'initializeUploadRequest' => [
                    'owner' => $memberUrn,
                ],
            ]);

        $this->assertSuccessful($initResponse, 'Failed to initialize LinkedIn image upload.');

        $uploadUrl = $initResponse->json('value.uploadUrl');
        $imageUrn = $initResponse->json('value.image');

        $uploadResponse = Http::withToken($accessToken)->withBody($imageBytes, $contentType)->put($uploadUrl);

        if ($uploadResponse->failed()) {
            throw new LinkedInApiException(
                'Failed to upload image bytes to LinkedIn.',
                $uploadResponse->status(),
                $uploadResponse->body(),
            );
        }

        Log::info('LinkedIn image bytes uploaded', [
            'image_urn' => $imageUrn,
            'status' => $uploadResponse->status(),
            'bytes' => strlen($imageBytes),
            'content_type' => $contentType,
        ]);

        $this->waitForImageToBeAvailable($accessToken, $imageUrn);

        return $imageUrn;
    }

    /**
     * Poll the image until LinkedIn has finished processing it. A post that references an image
     * still processing (or one that failed processing) is accepted with a 201 but never renders.
     */
    private function waitForImageToBeAvailable(string $accessToken, string $imageUrn): void
    {
        $status = null;

        for ($attempt = 1; $attempt <= self::IMAGE_STATUS_POLL_ATTEMPTS; $attempt++) {
            $response = Http::withHeaders($this->restHeaders($accessToken))
                ->get('https://api.linkedin.com/rest/images/'.rawurlencode($imageUrn));

            $status = $response->json('status');

            Log::info('LinkedIn image status', [
                'image_urn' => $imageUrn,
                'attempt' => $attempt,
                'http_status' => $response->status(),
                'status' => $status,
            ]);

            if ($response->failed() || $status === 'AVAILABLE') {
                return;
            }

            if ($status === 'PROCESSING_FAILED') {
                throw new LinkedInApiException('LinkedIn failed to process the uploaded image.', $response->status(), $response->json() ?? $response->body());
            }

            Sleep::for(1)->second();
        }

        Log::warning('LinkedIn image still not AVAILABLE after polling; posting anyway', [
            'image_urn' => $imageUrn,
            'status' => $status,
        ]);
    }

    /**
     * Create a published, native image post on the member's profile. Returns the post URN
     * captured from the response's x-restli-id header.
     */
    private function createPost(string $accessToken, string $memberUrn, string $imageUrn, string $caption): string
    {
        $response = Http::withHeaders($this->restHeaders($accessToken))
            ->post('https://api.linkedin.com/rest/posts', [
                'author' => $memberUrn,
                'commentary' => $caption,
                'visibility' => 'PUBLIC',
                'distribution' => [
                    'feedDistribution' => 'MAIN_FEED',
                    'targetEntities' => [],
                    'thirdPartyDistributionChannels' => [],
                ],
                'content' => [
                    'media' => ['id' => $imageUrn],
                ],
                'lifecycleState' => 'PUBLISHED',
                'isReshareDisabledByAuthor' => false,
            ]);

        Log::info('LinkedIn create post response', [
            'status' => $response->status(),
            'restli_id' => $response->header('x-restli-id'),
            'body' => $response->body(),
        ]);

        $this->assertSuccessful($response, 'Failed to create LinkedIn post.');

        $postUrn = $response->header('x-restli-id');

        if (! $postUrn) {
            throw new LinkedInApiException('LinkedIn accepted the post but returned no post URN.', $response->status(), $response->body());
        }

        $this->assertPostIsPublished($accessToken, $postUrn);

        return $postUrn;
    }

    /**
     * Read the post back and log its real state. Reading member posts needs r_member_social, which
     * this app may not have, so an unreadable post is logged and tolerated. A readable post that
     * isn't PUBLISHED is a failure: LinkedIn issued a URN for something that won't render.
     */
    private function assertPostIsPublished(string $accessToken, string $postUrn): void
    {
        $response = Http::withHeaders($this->restHeaders($accessToken))
            ->get('https://api.linkedin.com/rest/posts/'.rawurlencode($postUrn));

        Log::info('LinkedIn post verification response', [
            'post_urn' => $postUrn,
            'status' => $response->status(),
            'lifecycle_state' => $response->json('lifecycleState'),
            'visibility' => $response->json('visibility'),
            'body' => $response->body(),
        ]);

        if ($response->successful() && $response->json('lifecycleState') !== 'PUBLISHED') {
            throw new LinkedInApiException(
                "LinkedIn created {$postUrn} but its lifecycleState is not PUBLISHED.",
                $response->status(),
                $response->json() ?? $response->body(),
            );
        }
    }

    /**
     * @return array<string, string>
     */
    private function restHeaders(string $accessToken): array
    {
        return [
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type' => 'application/json',
            'LinkedIn-Version' => self::API_VERSION,
            'X-Restli-Protocol-Version' => '2.0.0',
        ];
    }

    private function assertSuccessful(Response $response, string $message): void
    {
        if ($response->failed()) {
            throw new LinkedInApiException($message, $response->status(), $response->json() ?? $response->body());
        }
    }
}
