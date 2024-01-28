<?php

namespace Tests\Unit\Http\Requests\Api\Station;

use App\Http\Requests\Api\Station\UpdateStationRequest;
use Tests\Unit\Http\Requests\Api\FormRequestTest;

/**
 * @see UpdateStationRequest
 */
class UpdateStationRequestTest extends FormRequestTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->rules     = (new UpdateStationRequest())->rules();
        $this->validator = $this->app['validator'];
    }

    public function testValidName()
    {
        $this->assertTrue($this->validateField('name', 'Capuccin Rainforest 23'));
    }

    public function testValidAddress()
    {
        $this->assertTrue($this->validateField('name', '423 Kihn Corners, South Marco, IA 61917'));
    }

    public function testValidCompanyId()
    {
        $this->assertTrue($this->validateField('companyId', 321));
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
