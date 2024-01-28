<?php

namespace Tests\Unit\Http\Requests\Api\Company;

use App\Http\Requests\Api\Company\StoreCompanyRequest;
use Tests\Unit\Http\Requests\Api\FormRequestTest;

/**
 * @see StoreCompanyRequest
 */
class StoreCompanyRequestTest extends FormRequestTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->rules     = (new StoreCompanyRequest())->rules();
        $this->validator = $this->app['validator'];
    }

    public function testValidName()
    {
        $this->assertTrue($this->validateField('name', 'Rheinmetall EVs'));
        $this->assertTrue($this->validateField('name', '23 & Me EVs'));
    }

    public function testValidParentId()
    {
        $this->assertTrue($this->validateField('parentId', 45));
        $this->assertTrue($this->validateField('parentId', null));
    }
}
