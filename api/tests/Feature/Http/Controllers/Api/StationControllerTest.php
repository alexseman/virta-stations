<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\StationController;
use App\Http\Requests\Api\Station\SearchStationRequest;
use App\Http\Requests\Api\Station\StoreStationRequest;
use App\Http\Requests\Api\Station\UpdateStationRequest;
use App\Models\Company;
use App\Models\Station;
use App\Models\VirtaModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see StationController
 */
class StationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected static function createStationWithCompany(): array
    {
        $company = Company::factory()->create();
        $station = Station::factory()->create(['company_id' => $company->id]);

        return compact('company', 'station');
    }

    protected static function createStationsWithCompanies(
        $companiesCount = 10,
        $stationsCount = 40
    ): array {
        $companies = Company::factory()->times($companiesCount)->create();
        $stations  = [];
        $i         = $stationsCount;

        do {
            $stations[] = Station::factory()->createOne(
                ['company_id' => ($companies->get(rand(0, $companiesCount - 1)))->id]
            );
            $i--;
        } while ($i > 0);

        return compact('companies', 'stations', 'companiesCount', 'stationsCount');
    }

    /**
     * @test
     */
    public function destroyReturnsAnOkResponse(): void
    {
        extract(self::createStationWithCompany());

        $response = $this->deleteJson(route('stationsstations.destroy', [$station]));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
        ]);

        $this->assertModelMissing($station);
    }

    /**
     * @test
     */
    public function indexReturnsAnOkResponse(): void
    {
        extract(self::createStationsWithCompanies());

        $response = $this->getJson(route('stationsstations.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data'  => [],
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'links' => [],
                'path',
                'per_page',
                'to',
                'total',
            ],
        ]);

        $this->assertEquals(
            ($stationsCount / VirtaModel::PAGINATION_PER_PAGE),
            $response->json('meta.last_page')
        );
        $this->assertEquals($stationsCount, $response->json('meta.total'));
        $this->assertEquals(VirtaModel::PAGINATION_PER_PAGE, $response->json('meta.per_page'));
    }

    /**
     * @test
     */
    public function searchValidatesWithAFormRequest(): void
    {
        $this->assertActionUsesFormRequest(
            StationController::class,
            'search',
            SearchStationRequest::class
        );
    }

    /**
     * @test
     */
    public function searchReturnsAnOkResponse(): void
    {
        extract(self::createStationsWithCompanies(stationsCount: 100));

        $response = $this->getJson(
            route('stations', ['lat' => 30, 'long' => 31, 'radius' => 10000])
        );

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data',
        ]);
    }

    /**
     * @test
     */
    public function showReturnsAnOkResponse(): void
    {
        extract(self::createStationWithCompany());

        $response = $this->getJson(route('stationsstations.show', [$station]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'address',
                'location' => [
                    'type',
                    'coordinates',
                ],
                'company_id',
                'created_at',
                'updated_at',
                'company' => [
                    'id',
                    'name',
                    'parent_id',
                    'created_at',
                    'updated_at',
                ],
            ],
            'success',
        ]);

        $this->assertEquals($station->id, $response->json('data.id'));
        $this->assertEquals($station->name, $response->json('data.name'));
        $this->assertEquals($company->id, $response->json('data.company_id'));
        $this->assertEquals($company->id, $response->json('data.company.id'));
        $this->assertEquals($company->name, $response->json('data.company.name'));
        $this->assertEquals(null, $response->json('data.company.parent_id'));
    }

    /**
     * @test
     */
    public function storeValidatesWithAFormRequest(): void
    {
        $this->assertActionUsesFormRequest(
            StationController::class,
            'store',
            StoreStationRequest::class
        );
    }

    /**
     * @test
     */
    public function storeReturnsAnOkResponse(): void
    {
        $company           = Company::factory()->create();
        $newStationName    = 'Riverside Charging';
        $newStationAddress = '9104 Jacobi Extension Tessiemouth, MO 70594-1035';
        $newStationLat     = 25.734687;
        $newStationLong    = 75.307376;
        $response          = $this->postJson(route('stationsstations.store'), [
            'name'      => $newStationName,
            'lat'       => $newStationLat,
            'long'      => $newStationLong,
            'address'   => $newStationAddress,
            'companyId' => $company->id
        ]);

        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'address',
                'location' => [
                    'type',
                    'coordinates',
                ],
                'company_id',
                'created_at',
                'updated_at',
                'company' => [
                    'id',
                    'name',
                    'parent_id',
                    'created_at',
                    'updated_at',
                ],
            ],
            'success',
            'message',
        ]);

        $this->assertEquals($newStationName, $response->json('data.name'));
        $this->assertEquals($newStationLong, $response->json('data.location.coordinates')[0]);
        $this->assertEquals($newStationLat, $response->json('data.location.coordinates')[1]);
        $this->assertEquals($newStationAddress, $response->json('data.address'));
        $this->assertEquals($company->id, $response->json('data.company_id'));
        $this->assertEquals($company->id, $response->json('data.company.id'));
        $this->assertEquals($company->name, $response->json('data.company.name'));
        $this->assertEquals(null, $response->json('data.company.parent_id'));
    }

    /**
     * @test
     */
    public function updateValidatesWithAFormRequest(): void
    {
        $this->assertActionUsesFormRequest(
            StationController::class,
            'update',
            UpdateStationRequest::class
        );
    }

    /**
     * @test
     */
    public function updateReturnsAnOkResponse(): void
    {
        extract(self::createStationWithCompany());

        $updatedCompany        = Company::factory()->create();
        $updatedStationName    = 'Connelly 24 EV';
        $updatedStationAddress = '340 Cory Course South Eldon, VT 96142';
        $updatedStationLat     = -177.534949;
        $updatedStationLong    = -68.307831;

        $response = $this->putJson(route('stationsstations.update', [$station]), [
            'name'      => $updatedStationName,
            'lat'       => $updatedStationLat,
            'long'      => $updatedStationLong,
            'address'   => $updatedStationAddress,
            'companyId' => $updatedCompany->id
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'address',
                'location' => [
                    'type',
                    'coordinates',
                ],
                'company_id',
                'created_at',
                'updated_at',
                'company' => [
                    'id',
                    'name',
                    'parent_id',
                    'created_at',
                    'updated_at',
                ],
            ],
            'success',
            'message',
        ]);

        $this->assertEquals($updatedStationName, $response->json('data.name'));
        $this->assertEquals($updatedStationLong, $response->json('data.location.coordinates')[0]);
        $this->assertEquals($updatedStationLat, $response->json('data.location.coordinates')[1]);
        $this->assertEquals($updatedStationAddress, $response->json('data.address'));
        $this->assertEquals($updatedCompany->id, $response->json('data.company_id'));
        $this->assertEquals($updatedCompany->id, $response->json('data.company.id'));
        $this->assertEquals($updatedCompany->name, $response->json('data.company.name'));
        $this->assertEquals(null, $response->json('data.company.parent_id'));
    }
}
