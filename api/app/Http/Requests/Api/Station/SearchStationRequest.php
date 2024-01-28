<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Station;

use App\Dto\Station\SearchStationDto;
use App\Http\Requests\Api\ApiRequest;
use App\Validation\Company\ValidateStationCompanyExists;
use App\Validation\Company\ValidateStationLocationCoords;

/**
 * @property mixed $lat
 * @property mixed $long
 * @property mixed $radius
 * @property mixed $companyId
 */
class SearchStationRequest extends ApiRequest
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
            'lat'       => 'required|numeric|min:-90|max:90',
            'long'      => 'required|numeric|min:-180|max:180',
            'radius'    => 'required|integer',
            'companyId' => 'integer',
        ];
    }

    public function prepareForValidation(): void
    {
        $fields = [
            'lat'    => floatval($this->lat),
            'long'   => floatval($this->long),
            'radius' => intval($this->radius)
        ];

        if ($this->companyId) {
            $fields['companyId'] = intval($this->companyId);
        }

        $this->merge($fields);
    }

    public function after(): array
    {
        $after = [
            new ValidateStationLocationCoords(),
        ];

        if ($this->companyId) {
            $after[] = new ValidateStationCompanyExists();
        }

        return $after;
    }

    public function data(): SearchStationDto
    {
        return new SearchStationDto(
            $this->validated('radius'),
            $this->validated('lat'),
            $this->validated('long'),
            $this->validated('companyId'),
        );
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'radius.required'   => 'A radius in KM around the point of search must be provided',
            'radius.integer'    => 'An integer value representing the KM around the point of search must be provided',
            'lat.required'      => 'A latitude for the initial point of search must be provided',
            'long.required'     => 'A longitude for the initial point of search must be provided',
            'lat.numeric'       => 'A valid numeric value for latitude must be provided',
            'long.numeric'      => 'A valid numeric value for longitude must be provided',
            'lat.min'           => 'Latitude ranges between -90 and 90 degrees',
            'lat.max'           => 'Latitude ranges between -90 and 90 degrees',
            'long.min'          => 'Longitude ranges between -180 and 180 degrees',
            'long.max'          => 'Longitude ranges between -180 and 180 degrees',
            'companyId.integer' => 'A station\'s company ID must be an integer',
        ];
    }
}
