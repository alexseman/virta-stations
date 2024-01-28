<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Company\StoreCompanyRequest;
use App\Http\Requests\Api\Company\UpdateCompanyRequest;
use App\Http\Resources\Api\CompanyResource;
use App\Http\Resources\Api\StationResource;
use App\Models\Company;
use App\Models\VirtaModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/companies",
     *     summary="Companies Index",
     *     description="Returns a paginated index of the companies",
     *     operationId="CompaniesIndex",
     *     tags={"companies", "index", "get"},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiResource")
     *      )
     * )
     *
     * @param Request $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $page    = $request->query('page') ?? 1;
        $perPage = $request->query('per_page') ?? VirtaModel::PAGINATION_PER_PAGE;

        // I'm sure this can be done much better
        $companies = \Cache::remember(
            Company::CACHE_KEY_COMPANIES_FLAT_MAP,
            3600 * 24,
            fn () => (Company::defaultOrder())->get()->toFlatTree() // @phpstan-ignore-line
        );
        $count  = $companies->count();
        $offset = $perPage * ($page - 1);

        $paginatedResources = $companies->skip($offset)->take($perPage)->values();
        $paginator          = new LengthAwarePaginator(
            $paginatedResources,
            $count,
            $perPage,
            $page
        );

        return CompanyResource::collection($paginator);
    }

    /**
     * @OA\Post(
     *      path="/companies",
     *      summary="Companies Store/Create",
     *      description="Returns a list of companies that meet the search criteria",
     *      operationId="CompaniesStore",
     *      tags={"companies", "store", "post"},
     *
     *      @OA\RequestBody(
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          description="Company name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="parent_id",
     *                          description="The ID of the eventual parent company",
     *                          type="int|null"
     *                      )
     *                 ),
     *                 example={
     *                     "name": "Greenfelder PLC",
     *                     "parent_id": 234
     *                }
     *             )
     *         )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="success",
     *                  type="bool",
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  oneOf={@OA\Schema(type="CompanyResource")}
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="success",
     *                  type="bool",
     *              ),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object"
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="success",
     *                  type="bool",
     *              ),
     *              @OA\Property(
     *                  property="errors",
     *                  type="array",
     *
     *                  @OA\Items(type="string")
     *              )
     *          )
     *      )
     *  )
     *
     * @param StoreCompanyRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $company = new Company([
            'name'      => $request->data()->getName(),
            'parent_id' => $request->data()->getParentId(),
        ]);

        try {
            $company->saveOrFail();
        } catch (\Throwable $e) {
            $this->respondWithException(
                errors: [
                    'transaction_failure' => $e->getMessage(),
                ],
                status: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return (new CompanyResource($company))
            ->withSuccess(true)
            ->withMessage('Successfully created company')
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *      path="/companies/{companyId}",
     *      summary="Companies Edit/Update",
     *      description="Fetches information about a single company together with its eventual subsidiaries and parent companies",
     *      operationId="CompaniesShow",
     *      tags={"companies", "show", "get"},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="success",
     *                  type="bool",
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  oneOf={@OA\Schema(type="CompanyResource")}
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="success",
     *                  type="bool",
     *              ),
     *              @OA\Property(
     *                  property="errors",
     *                  type="array",
     *
     *                  @OA\Items(type="string")
     *              )
     *          )
     *      )
     *  )
     *
     * @param Company $company
     *
     * @return JsonResponse
     */
    public function show(Company $company): JsonResponse
    {
        return (new CompanyResource($company))
            ->withSuccess(true)
            ->withExtraDataKeyValue(
                'stations',
                StationResource::collection($company->allStationsCollection())
            )
            ->withExtraDataKeyValue('subsidiaries', $company->subsidiaries())
            ->withExtraDataKeyValue('parent_companies', $company->parentCompanies())
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Patch(
     *      path="/companies/{companyId}",
     *      summary="Companies Update",
     *      description="Update a company",
     *      operationId="CompaniesUpdate",
     *      tags={"companies", "update", "patch"},
     *
     *      @OA\RequestBody(
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          description="Company name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="parent_id",
     *                          description="The ID of the parent company",
     *                          type="int|null"
     *                      )
     *                 )
     *             )
     *         )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="success",
     *                  type="bool",
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  oneOf={@OA\Schema(type="CompanyResource")}
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="success",
     *                  type="bool",
     *              ),
     *              @OA\Property(
     *                  property="errors",
     *                  type="array",
     *
     *                  @OA\Items(type="string")
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="success",
     *                  type="bool",
     *              ),
     *              @OA\Property(
     *                  property="errors",
     *                  type="array",
     *
     *                  @OA\Items(type="string")
     *              )
     *          )
     *      )
     *  )
     *
     * @param UpdateCompanyRequest $request
     * @param Company              $company
     *
     * @return JsonResponse
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $company->name      = $request->data()->getName();
        $company->parent_id = $request->data()->getParentId();

        try {
            $company->saveOrFail();
        } catch (\Throwable $e) {
            $this->respondWithException(
                errors: [
                    'transaction_failure' => $e->getMessage(),
                ],
                status: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return (new CompanyResource($company))
            ->withSuccess(true)
            ->withMessage('Successfully updated company')
            ->withExtraDataKeyValue('subsidiaries', $company->subsidiaries())
            ->withExtraDataKeyValue('parent_companies', $company->parentCompanies())
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *      path="/companies/{companyId}",
     *      summary="Company Delete",
     *      description="Deletes a company (not a soft delete)",
     *      operationId="CompaniesDelete",
     *      tags={"companies", "delete"},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="success",
     *                  type="bool",
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="success",
     *                  type="bool",
     *              ),
     *              @OA\Property(
     *                  property="errors",
     *                  type="array",
     *
     *                  @OA\Items(type="string")
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(
     *                  property="success",
     *                  type="bool",
     *              ),
     *              @OA\Property(
     *                  property="errors",
     *                  type="array",
     *
     *                  @OA\Items(type="string")
     *              )
     *          )
     *      )
     *  )
     *
     * @param Company $company
     *
     * @return JsonResponse
     */
    public function destroy(Company $company): JsonResponse
    {
        try {
            $company->deleteOrFail();
        } catch (\Throwable $e) {
            $this->respondWithException(
                errors: [
                    'deletion_failure' => $e->getMessage(),
                ],
                status: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->respondWithSuccess(
            message: 'Successfully deleted company and its (eventual) subsidiaries and stations'
        );
    }
}
