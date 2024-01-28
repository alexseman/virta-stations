<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Virta Stations API",
 *     version="0.0.1",
 *
 *     @OA\Contact(
 *          email="mail@alexseman.com"
 *      )
 * )
 */
class ApiController extends Controller
{
    use ApiResponse;
}
