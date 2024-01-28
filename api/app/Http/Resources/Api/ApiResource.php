<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Traits\ApiResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="ApiResource"
 * )
 *
 * @OA\Property(
 *     property="success",
 *     type="bool"
 * )
 * @OA\Property(
 *     property="message",
 *     type="string"
 * )
 * @OA\Property(
 *     property="errors",
 *     type="array",
 *
 *     @OA\Items(type="string")
 * )
 *
 * @OA\Property(
 *     property="data",
 *     type="array",
 *
 *     @OA\Items(anyOf={
 *
 *         @OA\Schema(type="CompanyResource"),
 *         @OA\Schema(type="StationResource")
 *     })
 * )
 */
class ApiResource extends JsonResource
{
    use ApiResponse;

    protected array $outputTransform;
    protected array $extraDataKeyValues;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->extraDataKeyValues = [];
    }

    public function withSuccess(bool $success): ApiResource
    {
        $this->with['success'] = $success;
        return $this;
    }

    public function withMessage(string $message): ApiResource
    {
        $this->with['message'] = $message;
        return $this;
    }

    public function withExtraDataKeyValue(string $key, mixed $value): ApiResource
    {
        $this->extraDataKeyValues[$key] = $value;
        return $this;
    }

    protected function getOutputTransform(): array
    {
        return $this->outputTransform;
    }

    protected function setOutputTransform(array $outputTransform): void
    {
        $this->outputTransform = array_merge($outputTransform, $this->extraDataKeyValues);
    }
}
