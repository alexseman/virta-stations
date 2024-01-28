<?php

namespace Tests\Unit\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Tests\TestCase;

/**
 * @see FormRequest
 */
class FormRequestTest extends TestCase
{
    private $validator;
    protected array $rules;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = $this->app['validator'];
    }

    /**
     * Check a field and value against validation rule
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    protected function validateField(string $field, mixed $value): bool
    {
        return $this->validator->make(
            [$field => $value],
            [$field => $this->rules[$field]]
        )->passes();
    }
}
