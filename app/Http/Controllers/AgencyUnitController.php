<?php

namespace App\Http\Controllers;

use App\Events\MasterDataChanged;
use App\Http\Requests\StoreAgencyUnitRequest;
use App\Http\Resources\AgencyUnitResource;
use App\Models\AgencyUnit;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AgencyUnitController extends Controller
{
    /**
     * @OA\Get(
     *      security={{"bearerAuth": {}}},
     *      path="/agency-units",
     *      tags={"AgencyUnit"},
     *      summary="List of AgencyUnit",
     *
     *      @OA\Parameter(in="query", required=false, name="filter[name]", @OA\Schema(type="string"), example="keyword"),
     *      @OA\Parameter(in="query", required=false, name="filter[keyword]", @OA\Schema(type="string"), example="keyword"),
     *      @OA\Parameter(in="query", required=false, name="sort", @OA\Schema(type="string"), example="name"),
     *      @OA\Parameter(in="query", required=false, name="page", @OA\Schema(type="string"), example="1"),
     *      @OA\Parameter(in="query", required=false, name="rows", @OA\Schema(type="string"), example="10"),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function index(Request $request)
    {
        $rows = 10;
        if ($request->filled('rows')) {
            $rows = $request->rows;
        }

        $perPage = $request->query('per_page', $rows);

        $agencyUnits = QueryBuilder::for(AgencyUnit::class)
            ->allowedFilters([
                AllowedFilter::callback(
                    'keyword',
                    fn (Builder $query, $value) => $query->where('name', 'like', '%'.$value.'%')
                ),
                AllowedFilter::exact('id'),
                'name',
            ])
            ->allowedSorts('name', 'created_at')
            ->paginate($perPage)
            ->appends($request->query());

        return AgencyUnitResource::collection($agencyUnits);
    }

    /**
     * @OA\Post(
     *      security={{"bearerAuth": {}}},
     *      path="/agency-units",
     *      tags={"AgencyUnit"},
     *      summary="Store AgencyUnit",
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="agency_id", ref="#/components/schemas/AgencyUnit/properties/agency_id"),
     *              @OA\Property(property="name", ref="#/components/schemas/AgencyUnit/properties/name"),
     *              @OA\Property(property="acronym", ref="#/components/schemas/AgencyUnit/properties/acronym"),
     *         ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Data sukses disimpan."),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response="422",
     *          description="error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="agency_id", type="array", @OA\Items(example={"agency_id field is required."})),
     *              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
     *              @OA\Property(property="acronym", type="array", @OA\Items(example={"acronym field is required."})),
     *          ),
     *      ),
     * )
     */
    public function store(StoreAgencyUnitRequest $request)
    {
        $user = auth('custom')->user();
        $operation = 'Menambah';
        $entityType = 'agency unit';
        $entityName = $request->name;
        $status = 'created';

        try {
            $agencyUnit = AgencyUnit::create($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new AgencyUnitResource($agencyUnit), 'Data berhasil disimpan.', 201);
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to create agency unit: '.$e->getMessage());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendError('Gagal menyimpan data.', 500);
        }
    }

    /**
     * @OA\Get(
     *      security={{"bearerAuth": {}}},
     *      path="/agency-units/{id}",
     *      tags={"AgencyUnit"},
     *      summary="AgencyUnit details",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="AgencyUnit ID"),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function show(AgencyUnit $agencyUnit)
    {
        return $this->sendSuccess(new AgencyUnitResource($agencyUnit), 'Data berhasil ditampilkan.');
    }

    /**
     * @OA\Put(
     *      security={{"bearerAuth": {}}},
     *      path="/agency-units/{id}",
     *      tags={"AgencyUnit"},
     *      summary="Update AgencyUnit",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="AgencyUnit ID"),
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="agency_id", ref="#/components/schemas/AgencyUnit/properties/agency_id"),
     *              @OA\Property(property="name", ref="#/components/schemas/AgencyUnit/properties/name"),
     *              @OA\Property(property="acronym", ref="#/components/schemas/AgencyUnit/properties/acronym"),
     *         ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Data sukses disimpan."),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response="422",
     *          description="error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="agency_id", type="array", @OA\Items(example={"agency_id field is required."})),
     *              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
     *              @OA\Property(property="acronym", type="array", @OA\Items(example={"acronym field is required."})),
     *          ),
     *      ),
     * )
     */
    public function update(StoreAgencyUnitRequest $request, AgencyUnit $agencyUnit)
    {
        $user = auth('custom')->user();
        $operation = 'Mengubah';
        $entityType = 'agency unit';
        $entityName = $agencyUnit->name;
        $status = 'updated';

        try {
            $agencyUnit->update($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new AgencyUnitResource($agencyUnit), 'Data sukses disimpan.');
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to update agency unit: '.$e->getMessage());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return response()->json(['status_input' => null, 'error' => 'Update failed.'], 500);
        }
    }

    /**
     * @OA\Delete(
     *      security={{"bearerAuth": {}}},
     *      path="/agency-units/{id}",
     *      tags={"AgencyUnit"},
     *      summary="AgencyUnit Removal",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="AgencyUnit ID"),
     *
     *      @OA\Response(
     *          response=204,
     *          description="Response success no content",
     *      ),
     * )
     */
    public function destroy(AgencyUnit $agencyUnit)
    {
        $user = auth('custom')->user();
        $operation = 'Menghapus';
        $entityType = 'agency unit';
        $entityName = $agencyUnit->name;
        $status = 'deleted';

        try {
            $agencyUnit->delete();

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess([], null, 204);
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to delete agency unit: '.$e->getMessage());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendError('Gagal menghapus data.', 500);
        }
    }

    /**
     * @OA\Get(
     *      security={{"bearerAuth": {}}},
     *      path="/agency-units/schema",
     *      tags={"AgencyUnit"},
     *      summary="Schema of AgencyUnit",
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function schema(Request $request)
    {
        $fields = DB::select('describe mst_agency_units');
        $schema = [
            'name' => 'mst_agency_units',
            'module' => 'AgencyUnit',
            'primary_key' => 'id',
            'endpoint' => '/agency-units',
            'scheme' => array_values($fields),
        ];

        return $this->sendSuccess($schema, 'Data berhasil ditampilkan.');
    }
}
