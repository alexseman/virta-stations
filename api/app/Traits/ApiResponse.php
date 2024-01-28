<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response;

/**
 * Not ideal, mixed responsibilities - it is both an API Responder and a response formatter.
 */
trait ApiResponse
{
    public function respondWithResourceData(array $data): array
    {
        return [
            'data' => $data,
        ];
    }

    public function respondWithSuccess(
        ?string               $message = null,
        null|array|Collection $data = null,
        $status = Response::HTTP_OK
    ): JsonResponse {
        $response = ['success' => true];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    public function respondWithoutSuccess(MessageBag|array $errors, int $status): JsonResponse
    {
        return response()->json(['success' => false, 'errors' => $errors], $status);
    }

    public function respondWithException(
        MessageBag|array $errors,
        $status = Response::HTTP_UNPROCESSABLE_ENTITY
    ) {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors'  => $errors,
        ], $status));
    }
}
