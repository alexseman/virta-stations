<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Company;

use App\Dto\Company\StoreCompanyDto;
use App\Http\Requests\Api\ApiRequest;

class StoreCompanyRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'parentId' => 'nullable|integer',
        ];
    }

    public function data(): StoreCompanyDto
    {
        return new StoreCompanyDto(
            $this->validated('name'),
            $this->validated('parentId'),
        );
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'A company name is required',
            'name.string'   => 'A company name must be represented as a string',
            'name.max:255'  => 'A company name must be at most comprised of 255 characters',
        ];
    }
}
