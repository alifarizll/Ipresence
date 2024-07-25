<?php

namespace App\Http\Controllers;

use App\Events\MasterDataChanged;
use App\Http\Requests\StoreDistrictRequest;
use App\Http\Resources\DistrictResource;
use App\Models\District;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DistrictController extends Controller
{
    /**
     * @OA\Get(
     *      security={{"bearerAuth": {}}},
     *      path="/districts",
     *      tags={"District"},
     *      summary="List of District",
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

        $districts = QueryBuilder::for(District::class)
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

        return DistrictResource::collection($districts);
    }

    /**
     * @OA\Post(
     *      security={{"bearerAuth": {}}},
     *      path="/districts",
     *      tags={"District"},
     *      summary="Store District",
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="province_id", ref="#/components/schemas/District/properties/province_id"),
     *              @OA\Property(property="regency_id", ref="#/components/schemas/District/properties/regency_id"),
     *              @OA\Property(property="land_area", ref="#/components/schemas/District/properties/land_area"),
     *              @OA\Property(property="code", ref="#/components/schemas/District/properties/code"),
     *              @OA\Property(property="name", ref="#/components/schemas/District/properties/name"),
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
     *              @OA\Property(property="province_id", type="array", @OA\Items(example={"province_id field is required."})),
     *              @OA\Property(property="regency_id", type="array", @OA\Items(example={"regency_id field is required."})),
     *              @OA\Property(property="land_area", type="array", @OA\Items(example={"land_area field is required."})),
     *              @OA\Property(property="code", type="array", @OA\Items(example={"code field is required."})),
     *              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
     *          ),
     *      ),
     * )
     */
    public function store(StoreDistrictRequest $request)
    {
        $user = auth('custom')->user();
        $operation = 'Menambah';
        $entityType = 'kecamatan';
        $entityName = $request->name;
        $status = 'created';

        try {
            $district = District::create($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new DistrictResource($district), 'Data berhasil disimpan.', 201);
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to create district: '.$e->getMessage());

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
     *      path="/districts/{id}",
     *      tags={"District"},
     *      summary="District details",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="District ID"),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function show(District $district)
    {
        return $this->sendSuccess(new DistrictResource($district), 'Data berhasil ditampilkan.');
    }

    /**
     * @OA\Put(
     *      security={{"bearerAuth": {}}},
     *      path="/districts/{id}",
     *      tags={"District"},
     *      summary="Update District",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="District ID"),
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="province_id", ref="#/components/schemas/District/properties/province_id"),
     *              @OA\Property(property="regency_id", ref="#/components/schemas/District/properties/regency_id"),
     *              @OA\Property(property="land_area", ref="#/components/schemas/District/properties/land_area"),
     *              @OA\Property(property="code", ref="#/components/schemas/District/properties/code"),
     *              @OA\Property(property="name", ref="#/components/schemas/District/properties/name"),
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
     *              @OA\Property(property="province_id", type="array", @OA\Items(example={"province_id field is required."})),
     *              @OA\Property(property="regency_id", type="array", @OA\Items(example={"regency_id field is required."})),
     *              @OA\Property(property="land_area", type="array", @OA\Items(example={"land_area field is required."})),
     *              @OA\Property(property="code", type="array", @OA\Items(example={"code field is required."})),
     *              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
     *          ),
     *      ),
     * )
     */
    public function update(StoreDistrictRequest $request, District $district)
    {
        $user = auth('custom')->user();
        $operation = 'Mengubah';
        $entityType = 'kecamatan';
        $entityName = $district->name;
        $status = 'updated';

        try {
            $district->update($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new DistrictResource($district), 'Data sukses disimpan.');
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to update district: '.$e->getMessage());

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
     *      path="/districts/{id}",
     *      tags={"District"},
     *      summary="District Removal",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="District ID"),
     *
     *      @OA\Response(
     *          response=204,
     *          description="Response success no content",
     *      ),
     * )
     */
    public function destroy(District $district)
    {
        $user = auth('custom')->user();
        $operation = 'Menghapus';
        $entityType = 'kecamatan';
        $entityName = $district->name;
        $status = 'deleted';

        try {
            $district->delete();

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
            Log::error('Failed to delete district: '.$e->getMessage());

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
     *      path="/districts/schema",
     *      tags={"District"},
     *      summary="Schema of District",
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function schema(Request $request)
    {
        $fields = DB::select('describe mst_districts');
        $schema = [
            'name' => 'mst_districts',
            'module' => 'District',
            'primary_key' => 'id',
            'endpoint' => '/districts',
            'scheme' => array_values($fields),
        ];

        return $this->sendSuccess($schema, 'Data berhasil ditampilkan.');
    }
}
