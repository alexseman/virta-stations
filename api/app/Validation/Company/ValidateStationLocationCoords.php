<?php

declare(strict_types=1);

namespace App\Validation\Company;

use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Validation\Validator;

class ValidateStationLocationCoords
{
    public function __invoke(Validator $validator)
    {
        $lat  = $validator->getValue('lat');
        $long = $validator->getValue('long');

        if (! ($lat && $long)) {
            // if we are here then we are in a PATCH operation
            // in the POST and GET /search operations we have the "required" validation rule
            return;
        }

        try {
            Point::makeGeodetic($lat, $long);
        } catch (\Throwable $e) {
            $message = 'Failed to create Point geometry from given coords. Error: ' . $e->getMessage(
            );
            preg_match('/\$latitude|\$longitude/', $e->getMessage(), $matches);
            if (array_pop($matches) === '$latitude') {
                $validator->errors()->add('lat', $message);
            } elseif (array_pop($matches) === '$longitude') {
                $validator->errors()->add('long', $message);
            }
        }
    }
}
