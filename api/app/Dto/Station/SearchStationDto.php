<?php

declare(strict_types=1);

namespace App\Dto\Station;

use Clickbar\Magellan\Data\Geometries\Point;

class SearchStationDto
{
    public function __construct(
        public int   $radius,
        public float $lat,
        public float $long,
        public ?int  $companyId,
    ) {
    }

    /**
     * The radius is received in KM but will be employed in the search in meters.
     *
     * @return int
     */
    public function getRadius(): int
    {
        return $this->radius * 1000;
    }

    public function getStartingPoint(): Point
    {
        return Point::makeGeodetic($this->lat, $this->long);
    }

    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function getLong(): float
    {
        return $this->long;
    }
}
