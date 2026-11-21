<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\LinkedInApiException;
use App\Exceptions\LinkedInNotConnectedException;
use App\Http\Controllers\Controller;
use App\Services\LinkedInService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LinkedInPostController extends Controller
{
    public function store(Request $request, LinkedInService $linkedIn): JsonResponse
    {
        $data = $request->validate([
            'image_url' => ['required', 'url', 'max:2048'],
            'caption' => ['required', 'string', 'max:3000'],
        ]);

        try {
            $post = $linkedIn->publishImagePost($data['image_url'], $data['caption']);
        } catch (LinkedInNotConnectedException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (LinkedInApiException $e) {
            Log::error('LinkedIn post failed', [
                'message' => $e->getMessage(),
                'linkedin_status' => $e->status,
                'linkedin_response' => $e->responseBody,
                'image_url' => $data['image_url'],
            ]);

            // 500 rather than 502 so the JSON body isn't mistaken for (or masked as) a gateway error.
            return response()->json([
                'message' => $e->getMessage(),
                'linkedin_status' => $e->status,
                'linkedin_response' => $e->responseBody,
            ], 500);
        }

        return response()->json([
            'status' => 'posted',
            'post_urn' => $post['urn'],
            'post_url' => $post['url'],
        ], 201);
    }
}
