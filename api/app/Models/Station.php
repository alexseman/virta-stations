<?php

declare(strict_types=1);

namespace App\Models;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Station
 *
 * @property int                             $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\StationFactory            factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Station newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Station newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Station query()
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereUpdatedAt($value)
 *
 * @property string $name
 * @property mixed  $location
 * @property string $address
 * @property int    $company_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Station whereName($value)
 *
 * @property-read \App\Models\Company $company
 *
 * @mixin \Eloquent
 */
class Station extends VirtaModel
{
    use HasPostgisColumns;

    public const GIS_SRID             = 4326;
    public const CACHE_KEY_INDIVIDUAL = 'stations';

    protected $fillable = [
        'name',
        'location',
        'address',
        'company_id',
    ];

    protected array $postgisColumns = [
        'location' => [
            'type' => 'geometry',
            'srid' => self::GIS_SRID,
        ],
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public static function searchWithinRadiusFromStartingPoint(
        float $lat,
        float $long,
        int   $radius,
        ?int  $companyId
    ): Collection {
        return self::searchResultsArrayToCollection(
            $companyId ?
                self::getWithinRadiusFromStartingPointForCompany($lat, $long, $radius, $companyId)
                : self::getAllWithinRadiusFromStartingPoint($lat, $long, $radius)
        );
    }

    protected static function searchResultsArrayToCollection(array $searchResults): Collection
    {
        return collect($searchResults)
            ->transform(function ($result) {
                $stations = json_decode($result->stations);

                return count($stations) === 1 ?
                    self::getOrSetForever(array_pop($stations)) :
                    array_map(function ($stationId) {
                        return self::getOrSetForever($stationId);
                    }, $stations);
            });
    }

    protected static function getAllWithinRadiusFromStartingPoint(
        float $lat,
        float $long,
        int   $radius
    ): array {
        return DB::select(
            '
                SELECT json_agg(DISTINCT s.station_id) AS stations
                FROM (SELECT s.location,
                             s.id AS station_id,
                             ST_DistanceSphere(
                                     public.ST_GeomFromEWKT(:point):: geometry,
                                     s.location:: geometry
                             )    AS distance_to_starting_point
                      FROM stations s
                      WHERE ST_DistanceSphere(
                                    public.ST_GeomFromEWKT(:point):: geometry,
                                    s.location:: geometry
                            ) <= :radius) AS s
                GROUP BY s.location, s.distance_to_starting_point
                ORDER BY s.distance_to_starting_point;',
            [
                'point'  => self::getPointArgumentForEWKT($lat, $long),
                'radius' => $radius,
            ]
        );
    }

    protected static function getWithinRadiusFromStartingPointForCompany(
        float $lat,
        float $long,
        int   $radius,
        int   $companyId
    ): array {
        $company = Company::getOrSetForever($companyId);
        return DB::select(
            '
                SELECT json_agg(DISTINCT s.station_id) AS stations
                FROM (SELECT s.location,
                             s.id AS station_id,
                             ST_DistanceSphere(
                                     public.ST_GeomFromEWKT(:point):: geometry,
                                     s.location:: geometry
                             )    AS distance_to_starting_point
                        FROM stations s
                        JOIN companies c ON c.id = s.company_id
                        WHERE true
                            AND c._lft BETWEEN :company_lft AND :company_rgt
                            AND ST_DistanceSphere(
                                    public.ST_GeomFromEWKT(:point):: geometry,
                                    s.location:: geometry
                            ) <= :radius) AS s
                GROUP BY s.location, s.distance_to_starting_point
                ORDER BY s.distance_to_starting_point;',
            [
                'point'       => self::getPointArgumentForEWKT($lat, $long),
                'radius'      => $radius,
                'company_lft' => $company['_lft'],
                'company_rgt' => $company['_rgt'],
            ]
        );
    }

    protected static function getPointArgumentForEWKT(float $lat, float $long): string
    {
        return sprintf('SRID=%d;POINT(%f %f)', self::GIS_SRID, $lat, $long);
    }
}
