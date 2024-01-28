<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): JsonResponse
    {
        if ($e instanceof ModelNotFoundException) {
            return $this->respondWithoutSuccess(
                errors: [
                    'Not Found',
                    'Error message: ' . $e->getMessage()
                ],
                status: Response::HTTP_NOT_FOUND
            );
        }

        return $this->respondWithoutSuccess(
            errors: [
                'Unhandled internal server error',
                'Error message: ' . $e->getMessage()
            ],
            status: Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
