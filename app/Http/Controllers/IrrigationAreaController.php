<?php

namespace App\Http\Controllers;

use App\Events\MasterDataChanged;
use App\Http\Requests\StoreIrrigationAreaRequest;
use App\Http\Resources\IrrigationAreaResource;
use App\Models\IrrigationArea;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class IrrigationAreaController extends Controller
{
    /**
     * @OA\Get(
     *      security={{"bearerAuth": {}}},
     *      path="/irrigation-areas",
     *      tags={"IrrigationArea"},
     *      summary="List of IrrigationArea",
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

        $irrigationAreas = QueryBuilder::for(IrrigationArea::class)
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

        return IrrigationAreaResource::collection($irrigationAreas);
    }

    /**
     * @OA\Post(
     *      security={{"bearerAuth": {}}},
     *      path="/irrigation-areas",
     *      tags={"IrrigationArea"},
     *      summary="Store IrrigationArea",
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="authority", ref="#/components/schemas/IrrigationArea/properties/authority"),
     *              @OA\Property(property="code", ref="#/components/schemas/IrrigationArea/properties/code"),
     *              @OA\Property(property="name", ref="#/components/schemas/IrrigationArea/properties/name"),
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
     *              @OA\Property(property="authority", type="array", @OA\Items(example={"authority field is required."})),
     *              @OA\Property(property="code", type="array", @OA\Items(example={"code field is required."})),
     *              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
     *          ),
     *      ),
     * )
     */
    public function store(StoreIrrigationAreaRequest $request)
    {
        $user = auth('custom')->user();
        $operation = 'Menambah';
        $entityType = 'daerah irigasi';
        $entityName = $request->name;
        $status = 'created';

        try {
            $irrigationArea = IrrigationArea::create($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new IrrigationAreaResource($irrigationArea), 'Data berhasil disimpan.', 201);
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to create irrigation area: '.$e->getMessage());

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
     *      path="/irrigation-areas/{id}",
     *      tags={"IrrigationArea"},
     *      summary="IrrigationArea details",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="IrrigationArea ID"),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function show(IrrigationArea $irrigationArea)
    {
        return $this->sendSuccess(new IrrigationAreaResource($irrigationArea), 'Data berhasil ditampilkan.');
    }

    /**
     * @OA\Put(
     *      security={{"bearerAuth": {}}},
     *      path="/irrigation-areas/{id}",
     *      tags={"IrrigationArea"},
     *      summary="Update IrrigationArea",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="IrrigationArea ID"),
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="authority", ref="#/components/schemas/IrrigationArea/properties/authority"),
     *              @OA\Property(property="code", ref="#/components/schemas/IrrigationArea/properties/code"),
     *              @OA\Property(property="name", ref="#/components/schemas/IrrigationArea/properties/name"),
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
     *              @OA\Property(property="authority", type="array", @OA\Items(example={"authority field is required."})),
     *              @OA\Property(property="code", type="array", @OA\Items(example={"code field is required."})),
     *              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
     *          ),
     *      ),
     * )
     */
    public function update(StoreIrrigationAreaRequest $request, IrrigationArea $irrigationArea)
    {
        $user = auth('custom')->user();
        $operation = 'Mengubah';
        $entityType = 'daerah irigasi';
        $entityName = $irrigationArea->name;
        $status = 'updated';

        try {
            $irrigationArea->update($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new IrrigationAreaResource($irrigationArea), 'Data sukses disimpan.');
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to update irrigation area: '.$e->getMessage());

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
     *      path="/irrigation-areas/{id}",
     *      tags={"IrrigationArea"},
     *      summary="IrrigationArea Removal",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="IrrigationArea ID"),
     *
     *      @OA\Response(
     *          response=204,
     *          description="Response success no content",
     *      ),
     * )
     */
    public function destroy(IrrigationArea $irrigationArea)
    {
        $user = auth('custom')->user();
        $operation = 'Menghapus';
        $entityType = 'daerah irigasi';
        $entityName = $irrigationArea->name;
        $status = 'deleted';

        try {
            $irrigationArea->delete();

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
            Log::error('Failed to delete irrigation area: '.$e->getMessage());

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
     *      path="/irrigation-areas/schema",
     *      tags={"IrrigationArea"},
     *      summary="Schema of IrrigationArea",
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function schema(Request $request)
    {
        $fields = DB::select('describe mst_irrigation_areas');
        $schema = [
            'name' => 'mst_irrigation_areas',
            'module' => 'IrrigationArea',
            'primary_key' => 'id',
            'endpoint' => '/irrigation-areas',
            'scheme' => array_values($fields),
        ];

        return $this->sendSuccess($schema, 'Data berhasil ditampilkan.');
    }
}
