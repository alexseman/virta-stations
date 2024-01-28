<?php

declare(strict_types=1);

namespace App\Dto\Station;

use Clickbar\Magellan\Data\Geometries\Point;

class UpdateStationDto
{
    public function __construct(
        public ?string $name,
        public ?string $address,
        public ?int    $companyId,
        public ?float  $lat,
        public ?float  $long,
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function getLocation(): ?Point
    {
        if (! ($this->lat && $this->long)) {
            return null;
        }

        return Point::makeGeodetic($this->lat, $this->long);
    }
}
