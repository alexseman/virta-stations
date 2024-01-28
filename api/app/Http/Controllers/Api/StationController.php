<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Station\SearchStationRequest;
use App\Http\Requests\Api\Station\StoreStationRequest;
use App\Http\Requests\Api\Station\UpdateStationRequest;
use App\Http\Resources\Api\CompanyResource;
use App\Http\Resources\Api\StationResource;
use App\Models\Station;
use App\Models\VirtaModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class StationController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/stations",
     *     summary="Stations Index",
     *     description="Returns a paginated index of the stations",
     *     operationId="stationsIndex",
     *     tags={"stations", "index", "get"},
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
     *     @OA\Parameter(
     *         name="all",
     *         description="Overrides pagination and returns all the stations for use for /stations/by-company",
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
        if ($request->has('all')) {
            return StationResource::collection(Station::all());
        }

        $perPage = $request->query('per_page') ?? VirtaModel::PAGINATION_PER_PAGE;

        // if this endpoint would serve a UI where page numbers are not relevant/needed (e.g. an infinite scroll)
        // then a cursor paginator would be much more appropriate.
        $stations = Station::paginate($perPage);
        return StationResource::collection($stations);
    }

    /**
     * @OA\Get(
     *      path="/stations/search",
     *      summary="Stations Search",
     *      description="Returns a list of stations that meet the search criteria",
     *      operationId="stationsSearch",
     *      tags={"stations", "search", "get"},
     *
     *      @OA\Parameter(
     *          name="lat",
     *          description="Latitude for the starting point criteria of the stations search",
     *          in="query",
     *          required=true,
     *      ),
     *      @OA\Parameter(
     *          name="long",
     *          description="Longitude for the starting point criteria of the stations search",
     *          in="query",
     *          required=true,
     *      ),
     *      @OA\Parameter(
     *          name="radius",
     *          description="Indicates the KM radius of the search that is to be performed around the starting point",
     *          in="query",
     *          required=true,
     *      ),
     *      @OA\Parameter(
     *          name="company_id",
     *          description="If present the search will be performed only for the stations of the given company and its eventual subsidiaries",
     *          in="query",
     *          required=false,
     *      ),
     *
     *      @OA\Response(
     *           response=200,
     *           description="Successful operation",
     *
     *           @OA\JsonContent(ref="#/components/schemas/ApiResource")
     *      ),
     *
     *      @OA\Response(
     *           response=204,
     *           description="Successful operation but no results found meeting the criteria",
     *
     *           @OA\JsonContent(ref="#/components/schemas/ApiResource")
     *      )
     *  )
     *
     * @param SearchStationRequest $request
     *
     * @return JsonResponse
     */
    public function search(SearchStationRequest $request): JsonResponse
    {
        $searchResults = Station::searchWithinRadiusFromStartingPoint(
            $request->data()->getLat(),
            $request->data()->getLong(),
            $request->data()->getRadius(),
            $request->data()->getCompanyId()
        );
        return $this->respondWithSuccess(
            data  : $searchResults,
            status: $searchResults->count() ? Response::HTTP_OK : Response::HTTP_NO_CONTENT
        );
    }

    /**
     * @OA\Post(
     *      path="/stations",
     *      summary="Stations Store/Create",
     *      description="Returns a list of stations that meet the search criteria",
     *      operationId="stationsStore",
     *      tags={"stations", "store", "post"},
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
     *                          description="Station name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="address",
     *                          description="Station physical address",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="lat",
     *                          description="Latitude part of the station coordinates",
     *                          type="float"
     *                      ),
     *                      @OA\Property(
     *                          property="long",
     *                          description="Longitude part of the station coordinates",
     *                          type="float"
     *                      ),
     *                      @OA\Property(
     *                          property="company_id",
     *                          description="The company ID of the station",
     *                          type="int"
     *                      )
     *                 ),
     *                 example={
     *                     "name": "Riverside Lake 1",
     *                     "address": "3943 Winfield, Agustinaport, RI 15842-8736",
     *                     "lat": 122.704904,
     *                     "long": 71.593214,
     *                     "company_id": 15
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
     *                  oneOf={@OA\Schema(type="StationResource")}
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
     * @param StoreStationRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreStationRequest $request): JsonResponse
    {
        $station = new Station([
            'name'       => $request->data()->getName(),
            'address'    => $request->data()->getAddress(),
            'company_id' => $request->data()->getCompanyId(),
            'location'   => $request->data()->getLocation(),
        ]);

        try {
            $station->saveOrFail();
        } catch (\Throwable $e) {
            $this->respondWithException(
                errors: [
                    'transaction_failure' => $e->getMessage(),
                ],
                status: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return (new StationResource($station))
            ->withExtraDataKeyValue(
                'company',
                (new CompanyResource($station->company()->first()))->response()->getData()->data
            )
            ->withSuccess(true)
            ->withMessage('Successfully created station')
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *      path="/stations/{stationId}",
     *      summary="Stations Edit/Update",
     *      description="Fetches information about a single station together with informabion about its corresponding company",
     *      operationId="stationsShow",
     *      tags={"stations", "show", "get"},
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
     *                  oneOf={@OA\Schema(type="StationResource")}
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
     * @param Station $station
     *
     * @return JsonResponse
     */
    public function show(Station $station): JsonResponse
    {
        // This would deserve to have its own >single< SQL query instead of using the ORM
        return (new StationResource($station))
            ->withExtraDataKeyValue(
                'company',
                (new CompanyResource($station->company()->first()))->response()->getData()->data
            )
            ->withSuccess(true)
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Patch(
     *      path="/stations/{stationId}",
     *      summary="Stations Update",
     *      description="Update a station",
     *      operationId="stationsUpdate",
     *      tags={"stations", "update", "patch"},
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
     *                          description="Station name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="address",
     *                          description="Station physical address",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="lat",
     *                          description="Latitude part of the station coordinates. Required if long field is also present",
     *                          type="float"
     *                      ),
     *                      @OA\Property(
     *                          property="long",
     *                          description="Longitude part of the station coordinates. Requiref if lat field is also present",
     *                          type="float"
     *                      ),
     *                      @OA\Property(
     *                          property="company_id",
     *                          description="The company ID of the station",
     *                          type="int"
     *                      )
     *                 ),
     *                 example={
     *                     "name": "Riverside Lake 1",
     *                     "address": "3943 Winfield, Agustinaport, RI 15842-8736",
     *                     "lat": 122.704904,
     *                     "long": 71.593214,
     *                     "company_id": 15
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
     *                  oneOf={@OA\Schema(type="StationResource")}
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
     * @param UpdateStationRequest $request
     * @param Station              $station
     *
     * @return JsonResponse
     */
    public function update(UpdateStationRequest $request, Station $station): JsonResponse
    {
        if ($request->data()->getName()) {
            $station->name = $request->data()->getName();
        }
        if ($request->data()->getAddress()) {
            $station->address = $request->data()->getAddress();
        }
        if ($request->data()->getCompanyId()) {
            $station->company_id = $request->data()->getCompanyId();
        }
        if ($request->data()->getLocation()) {
            $station->location = $request->data()->getLocation();
        }

        try {
            $station->saveOrFail();
        } catch (\Throwable $e) {
            $this->respondWithException(
                errors: [
                    'transaction_failure' => $e->getMessage(),
                ],
                status: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return (new StationResource($station))
            ->withExtraDataKeyValue(
                'company',
                (new CompanyResource($station->company()->first()))->response()->getData()->data
            )
            ->withSuccess(true)
            ->withMessage('Successfully updated station')
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *      path="/stations/{stationId}",
     *      summary="Station Delete",
     *      description="Deletes a station (not a soft delete)",
     *      operationId="stationsDelete",
     *      tags={"stations", "delete"},
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
     * @param Station $station
     *
     * @return JsonResponse
     */
    public function destroy(Station $station): JsonResponse
    {
        try {
            $station->deleteOrFail();
        } catch (\Throwable $e) {
            $this->respondWithException(
                errors: [
                    'deletion_failure' => $e->getMessage(),
                ],
                status: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->respondWithSuccess(
            message: 'Successfully deleted station'
        );
    }
}
