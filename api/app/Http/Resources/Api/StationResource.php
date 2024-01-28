<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\Company;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="StationResource",
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
 *     property="company_id",
 *     type="int"
 * )
 *
 * @property int $company_id
 *
 * @OA\Property(
 *     property="name",
 *     type="string"
 * )
 *
 * @property string $name
 *
 * @OA\Property(
 *     property="address",
 *     type="string"
 * )
 *
 * @property string $address
 *
 * @OA\Property(
 *     property="location",
 *     type="Clickbar\Magellan\Data\Geometries\Point"
 * )
 *
 * @property Point $location
 *
 * @OA\Property(
 *     property="created_ad",
 *     type="Carbon"
 * )
 *
 * @property Carbon|null $created_at
 *
 * @OA\Property(
 *     property="updated_ad",
 *     type="Carbon"
 * )
 *
 * @property Carbon|null $updated_at
 *
 * @method Company company()
 */
class StationResource extends ApiResource
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
                                      'address'    => $this->address,
                                      'location'   => $this->location,
                                      'company_id' => $this->company_id,
                                      'created_at' => $this->created_at,
                                      'updated_at' => $this->updated_at,
                                  ]);

        return $this->getOutputTransform();
    }
}
