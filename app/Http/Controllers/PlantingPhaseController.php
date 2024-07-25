<?php

namespace App\Http\Controllers;

use App\Events\MasterDataChanged;
use App\Http\Requests\StorePlantingPhaseRequest;
use App\Http\Resources\PlantingPhaseResource;
use App\Models\PlantingPhase;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PlantingPhaseController extends Controller
{
    /**
     * @OA\Get(
     *      security={{"bearerAuth": {}}},
     *      path="/planting-phases",
     *      tags={"PlantingPhase"},
     *      summary="List of PlantingPhase",
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

        $plantingPhases = QueryBuilder::for(PlantingPhase::class)
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

        return PlantingPhaseResource::collection($plantingPhases);
    }

    /**
     * @OA\Post(
     *      security={{"bearerAuth": {}}},
     *      path="/planting-phases",
     *      tags={"PlantingPhase"},
     *      summary="Store PlantingPhase",
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="order_number", ref="#/components/schemas/PlantingPhase/properties/order_number"),
     *              @OA\Property(property="name", ref="#/components/schemas/PlantingPhase/properties/name"),
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
     *              @OA\Property(property="order_number", type="array", @OA\Items(example={"order_number field is required."})),
     *              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
     *          ),
     *      ),
     * )
     */
    public function store(StorePlantingPhaseRequest $request)
    {
        $user = auth('custom')->user();
        $operation = 'Menambah';
        $entityType = 'fase penanaman';
        $entityName = $request->name;
        $status = 'created';

        try {
            $plantingPhase = PlantingPhase::create($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new PlantingPhaseResource($plantingPhase), 'Data berhasil disimpan.', 201);
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to create planting phase: '.$e->getMessage());

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
     *      path="/planting-phases/{id}",
     *      tags={"PlantingPhase"},
     *      summary="PlantingPhase details",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="PlantingPhase ID"),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function show(PlantingPhase $plantingPhase)
    {
        return $this->sendSuccess(new PlantingPhaseResource($plantingPhase), 'Data berhasil ditampilkan.');
    }

    /**
     * @OA\Put(
     *      security={{"bearerAuth": {}}},
     *      path="/planting-phases/{id}",
     *      tags={"PlantingPhase"},
     *      summary="Update PlantingPhase",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="PlantingPhase ID"),
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="order_number", ref="#/components/schemas/PlantingPhase/properties/order_number"),
     *              @OA\Property(property="name", ref="#/components/schemas/PlantingPhase/properties/name"),
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
     *              @OA\Property(property="order_number", type="array", @OA\Items(example={"order_number field is required."})),
     *              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
     *          ),
     *      ),
     * )
     */
    public function update(StorePlantingPhaseRequest $request, PlantingPhase $plantingPhase)
    {
        $user = auth('custom')->user();
        $operation = 'Mengubah';
        $entityType = 'fase penanaman';
        $entityName = $plantingPhase->name;
        $status = 'updated';

        try {
            $plantingPhase->update($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new PlantingPhaseResource($plantingPhase), 'Data sukses disimpan.');
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to update planting phase: '.$e->getMessage());

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
     *      path="/planting-phases/{id}",
     *      tags={"PlantingPhase"},
     *      summary="PlantingPhase Removal",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="PlantingPhase ID"),
     *
     *      @OA\Response(
     *          response=204,
     *          description="Response success no content",
     *      ),
     * )
     */
    public function destroy(PlantingPhase $plantingPhase)
    {
        $user = auth('custom')->user();
        $operation = 'Menghapus';
        $entityType = 'fase penanaman';
        $entityName = $plantingPhase->name;
        $status = 'deleted';

        try {
            $plantingPhase->delete();

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
            Log::error('Failed to delete planting phase: '.$e->getMessage());

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
     *      path="/planting-phases/schema",
     *      tags={"PlantingPhase"},
     *      summary="Schema of PlantingPhase",
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function schema(Request $request)
    {
        $fields = DB::select('describe mst_planting_phases');
        $schema = [
            'name' => 'mst_planting_phases',
            'module' => 'PlantingPhase',
            'primary_key' => 'id',
            'endpoint' => '/planting-phases',
            'scheme' => array_values($fields),
        ];

        return $this->sendSuccess($schema, 'Data berhasil ditampilkan.');
    }
}
