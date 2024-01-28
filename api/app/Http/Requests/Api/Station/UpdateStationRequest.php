<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Station;

use App\Dto\Station\UpdateStationDto;
use App\Http\Requests\Api\ApiRequest;
use App\Validation\Company\ValidateStationCompanyExists;
use App\Validation\Company\ValidateStationLocationCoords;

class UpdateStationRequest extends ApiRequest
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
            'name'      => 'string|max:255',
            'address'   => 'string|max:255',
            'companyId' => 'integer',
            'lat'       => 'required_with:long|numeric|min:-90|max:90',
            'long'      => 'required_with:lat|numeric|min:-180|max:180',
        ];
    }

    public function after(): array
    {
        return [
            new ValidateStationLocationCoords(),
            new ValidateStationCompanyExists(),
        ];
    }

    public function data(): UpdateStationDto
    {
        return new UpdateStationDto(
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
            'name.string'       => 'A station name must be represented as a string',
            'name.max:255'      => 'A station name must be at most comprised of 255 characters',
            'address.string'    => 'A station address must be represented as a string',
            'address.max:255'   => 'A station address must be at most comprised of 255 characters',
            'companyId.integer' => 'A station\'s company ID must be an integer',
            'lat.numeric'       => 'A valid latitude must be provided',
            'long.numeric'      => 'A valid longitude must be provided',
            'lat.min'           => 'Latitude ranges between -90 and 90 degrees',
            'lat.max'           => 'Latitude ranges between -90 and 90 degrees',
            'long.min'          => 'Longitude ranges between -180 and 180 degrees',
            'long.max'          => 'Longitude ranges between -180 and 180 degrees',
        ];
    }
}
