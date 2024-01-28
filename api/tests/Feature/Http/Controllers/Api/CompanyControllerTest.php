<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\CompanyController;
use App\Http\Requests\Api\Company\StoreCompanyRequest;
use App\Http\Requests\Api\Company\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\VirtaModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see CompanyController
 */
class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function destroyReturnsAnOkResponse(): void
    {
        $company  = Company::factory()->create();
        $response = $this->deleteJson(route('companiescompanies.destroy', [$company]));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
        ]);

        $this->assertModelMissing($company);
    }

    /**
     * @test
     */
    public function indexReturnsAnOkResponse(): void
    {
        $count     = 32;
        $companies = Company::factory()->times($count)->create();
        $response  = $this->getJson(route('companiescompanies.index'));

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

        $this->assertEquals($count, $response->json('meta.total'));
        $this->assertEquals(VirtaModel::PAGINATION_PER_PAGE, $response->json('meta.per_page'));
    }

    /**
     * @test
     */
    public function showReturnsAnOkResponse(): void
    {
        $company  = Company::factory()->create();
        $response = $this->getJson(route('companiescompanies.show', [$company]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'parent_id',
                'created_at',
                'updated_at',
                'stations',
                'subsidiaries',
                'parent_companies',
            ],
            'success',
        ]);

        $this->assertEquals($company->id, $response->json('data.id'));
        $this->assertEquals($company->name, $response->json('data.name'));
        $this->assertEquals(null, $response->json('data.parent_id'));
    }

    /**
     * @test
     */
    public function storeValidatesWithAFormRequest(): void
    {
        $this->assertActionUsesFormRequest(
            CompanyController::class,
            'store',
            StoreCompanyRequest::class
        );
    }

    /**
     * @test
     */
    public function storeReturnsAnOkResponse(): void
    {
        $parentCompany  = Company::factory()->create();
        $newCompanyName = 'Company Test Name';
        $response       = $this->postJson(route('companiescompanies.store'), [
            'name'     => $newCompanyName,
            'parentId' => $parentCompany->id
        ]);

        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'parent_id',
                'created_at',
                'updated_at',
            ],
            'success',
            'message',
        ]);

        $this->assertEquals($newCompanyName, $response->json('data.name'));
        $this->assertEquals($parentCompany->id, $response->json('data.parent_id'));
    }

    /**
     * @test
     */
    public function updateValidatesWithAFormRequest(): void
    {
        $this->assertActionUsesFormRequest(
            CompanyController::class,
            'update',
            UpdateCompanyRequest::class
        );
    }

    /**
     * @test
     */
    public function updateReturnsAnOkResponse(): void
    {
        $company          = Company::factory()->create();
        $newParentCompany = Company::factory()->create();
        $newCompanyName   = 'New Company Name';

        $response = $this->putJson(route('companiescompanies.update', [$company]), [
            'name'     => $newCompanyName,
            'parentId' => $newParentCompany->id
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'parent_id',
                'created_at',
                'updated_at',
            ],
            'success'
        ]);

        $this->assertEquals($newCompanyName, $response->json('data.name'));
        $this->assertEquals($newParentCompany->id, $response->json('data.parent_id'));
    }
}
