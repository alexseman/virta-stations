<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="CompanyResource",
 * )
 *
 * @OA\Property(
 *     property="id",
 *     type="int"
 * )
 *
 * @property int $id
 *
 * @OA\Property(
 *     property="parent_companies",
 *     type="array",
 *
 *     @OA\Items(anyOf={@OA\Schema(type="CompanyResource")})
 * )
 *
 * @property array $parent_companies
 *
 * @OA\Property(
 *     property="subsidiaries",
 *     type="array",
 *
 *     @OA\Items(anyOf={@OA\Schema(type="CompanyResource")})
 * )
 *
 * @property array $subsidiaries
 *
 * @OA\Property(
 *     property="created_at",
 *     type="Carbon"
 * )
 *
 * @property Carbon|null $created_at
 *
 * @OA\Property(
 *     property="updated_at",
 *     type="Carbon"
 * )
 *
 * @property Carbon|null $updated_at
 *
 * @OA\Property(
 *     property="parent_id",
 *     type="int"
 * )
 *
 * @property int $parent_id
 *
 * @OA\Property(
 *     property="name",
 *     type="string"
 * )
 *
 * @property string $name
 */
class CompanyResource extends ApiResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->setOutputTransform([
                                      'id'         => $this->id,
                                      'name'       => $this->name,
                                      'parent_id'  => $this->parent_id,
                                      'created_at' => $this->created_at,
                                      'updated_at' => $this->updated_at,
                                  ]);

        return $this->getOutputTransform();
    }
}
