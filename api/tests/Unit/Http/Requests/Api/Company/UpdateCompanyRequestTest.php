<?php

namespace Tests\Unit\Http\Requests\Api\Company;

use App\Http\Requests\Api\Company\UpdateCompanyRequest;
use Tests\Unit\Http\Requests\Api\FormRequestTest;

/**
 * @see UpdateCompanyRequest
 */
class UpdateCompanyRequestTest extends FormRequestTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->rules     = (new UpdateCompanyRequest())->rules();
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
