<?php

namespace Tests\Unit\Http\Requests\Api\Station;

use App\Http\Requests\Api\Station\SearchStationRequest;
use Tests\Unit\Http\Requests\Api\FormRequestTest;

/**
 * @see SearchStationRequest
 */
class SearchStationRequestTest extends FormRequestTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->rules     = (new SearchStationRequest())->rules();
        $this->validator = $this->app['validator'];
    }

    public function testValidCompanyId()
    {
        $this->assertTrue($this->validateField('radius', 451));
    }

    public function testValidLatitude()
    {
        $this->assertTrue($this->validateField('lat', 25.734687));
        $this->assertTrue($this->validateField('lat', 45));
        $this->assertTrue($this->validateField('lat', -25));
        $this->assertTrue($this->validateField('lat', -77.534949));
    }

    public function testValidLongitude()
    {
        $this->assertTrue($this->validateField('long', 75.307376));
        $this->assertTrue($this->validateField('long', -68.307831));
        $this->assertTrue($this->validateField('long', -25));
        $this->assertTrue($this->validateField('long', 15));
    }
}
