<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Station;

use App\Dto\Station\StoreStationDto;
use App\Http\Requests\Api\ApiRequest;
use App\Validation\Company\ValidateStationCompanyExists;
use App\Validation\Company\ValidateStationLocationCoords;

class StoreStationRequest extends ApiRequest
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
            'name'      => 'required|string|max:255',
            'address'   => 'required|string|max:255',
            'companyId' => 'required|integer',
            'lat'       => 'required|numeric|min:-90|max:90',
            'long'      => 'required|numeric|min:-180|max:180',
        ];
    }

    public function after(): array
    {
        return [
            new ValidateStationLocationCoords(),
            new ValidateStationCompanyExists(),
        ];
    }

    public function data(): StoreStationDto
    {
        return new StoreStationDto(
            $this->validated('name'),
            $this->validated('address'),
            $this->validated('companyId'),
            $this->validated('lat'),
            $this->validated('long'),
        );
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'      => 'A station name is required',
            'name.string'        => 'A station name must be represented as a string',
            'name.max:255'       => 'A station name must be at most comprised of 255 characters',
            'address.required'   => 'A station address is required',
            'address.string'     => 'A station address must be represented as a string',
            'address.max:255'    => 'A station address must be at most comprised of 255 characters',
            'companyId.required' => 'A station company ID is required',
            'companyId.integer'  => 'A station\'s company ID must be an integer',
            'lat.numeric'        => 'A valid latitude must be provided',
            'long.numeric'       => 'A valid longitude must be provided',
            'lat.min'            => 'Latitude ranges between -90 and 90 degrees',
            'lat.max'            => 'Latitude ranges between -90 and 90 degrees',
            'long.min'           => 'Longitude ranges between -180 and 180 degrees',
            'long.max'           => 'Longitude ranges between -180 and 180 degrees',
        ];
    }
}
