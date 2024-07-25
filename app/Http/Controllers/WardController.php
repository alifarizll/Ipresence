<?php

namespace App\Http\Controllers;

use App\Events\MasterDataChanged;
use App\Http\Requests\StoreWardRequest;
use App\Http\Resources\WardResource;
use App\Models\Ward;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class WardController extends Controller
{
    /**
     * @OA\Get(
     *      security={{"bearerAuth": {}}},
     *      path="/wards",
     *      tags={"Ward"},
     *      summary="List of Ward",
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

        $wards = QueryBuilder::for(Ward::class)
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

        return WardResource::collection($wards);
    }

    /**
     * @OA\Post(
     *      security={{"bearerAuth": {}}},
     *      path="/wards",
     *      tags={"Ward"},
     *      summary="Store Ward",
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="district_id", ref="#/components/schemas/Ward/properties/district_id"),
     *              @OA\Property(property="province_id", ref="#/components/schemas/Ward/properties/province_id"),
     *              @OA\Property(property="land_area", ref="#/components/schemas/Ward/properties/land_area"),
     *              @OA\Property(property="regency_id", ref="#/components/schemas/Ward/properties/regency_id"),
     *              @OA\Property(property="code", ref="#/components/schemas/Ward/properties/code"),
     *              @OA\Property(property="name", ref="#/components/schemas/Ward/properties/name"),
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
     *              @OA\Property(property="district_id", type="array", @OA\Items(example={"district_id field is required."})),
     *              @OA\Property(property="province_id", type="array", @OA\Items(example={"province_id field is required."})),
     *              @OA\Property(property="land_area", type="array", @OA\Items(example={"land_area field is required."})),
     *              @OA\Property(property="regency_id", type="array", @OA\Items(example={"regency_id field is required."})),
     *              @OA\Property(property="code", type="array", @OA\Items(example={"code field is required."})),
     *              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
     *          ),
     *      ),
     * )
     */
    public function store(StoreWardRequest $request)
    {
        $user = auth('custom')->user();
        $operation = 'Menambah';
        $entityType = 'desa/kelurahan';
        $entityName = $request->name;
        $status = 'created';

        try {
            $ward = Ward::create($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new WardResource($ward), 'Data berhasil disimpan.', 201);
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to create ward: '.$e->getMessage());

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
     *      path="/wards/{id}",
     *      tags={"Ward"},
     *      summary="Ward details",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Ward ID"),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function show(Ward $ward)
    {
        return $this->sendSuccess(new WardResource($ward), 'Data berhasil ditampilkan.');
    }

    /**
     * @OA\Put(
     *      security={{"bearerAuth": {}}},
     *      path="/wards/{id}",
     *      tags={"Ward"},
     *      summary="Update Ward",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Ward ID"),
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="district_id", ref="#/components/schemas/Ward/properties/district_id"),
     *              @OA\Property(property="province_id", ref="#/components/schemas/Ward/properties/province_id"),
     *              @OA\Property(property="land_area", ref="#/components/schemas/Ward/properties/land_area"),
     *              @OA\Property(property="regency_id", ref="#/components/schemas/Ward/properties/regency_id"),
     *              @OA\Property(property="code", ref="#/components/schemas/Ward/properties/code"),
     *              @OA\Property(property="name", ref="#/components/schemas/Ward/properties/name"),
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
     *              @OA\Property(property="district_id", type="array", @OA\Items(example={"district_id field is required."})),
     *              @OA\Property(property="province_id", type="array", @OA\Items(example={"province_id field is required."})),
     *              @OA\Property(property="land_area", type="array", @OA\Items(example={"land_area field is required."})),
     *              @OA\Property(property="regency_id", type="array", @OA\Items(example={"regency_id field is required."})),
     *              @OA\Property(property="code", type="array", @OA\Items(example={"code field is required."})),
     *              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
     *          ),
     *      ),
     * )
     */
    public function update(StoreWardRequest $request, Ward $ward)
    {
        $user = auth('custom')->user();
        $operation = 'Mengubah';
        $entityType = 'desa/kelurahan';
        $entityName = $ward->name;
        $status = 'updated';

        try {
            $ward->update($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new WardResource($ward), 'Data sukses disimpan.');
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to update ward: '.$e->getMessage());

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
     *      path="/wards/{id}",
     *      tags={"Ward"},
     *      summary="Ward Removal",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Ward ID"),
     *
     *      @OA\Response(
     *          response=204,
     *          description="Response success no content",
     *      ),
     * )
     */
    public function destroy(Ward $ward)
    {
        $user = auth('custom')->user();
        $operation = 'Menghapus';
        $entityType = 'desa/kelurahan';
        $entityName = $ward->name;
        $status = 'deleted';

        try {
            $ward->delete();

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
            Log::error('Failed to delete ward: '.$e->getMessage());

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
     *      path="/wards/schema",
     *      tags={"Ward"},
     *      summary="Schema of Ward",
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function schema(Request $request)
    {
        $fields = DB::select('describe mst_wards');
        $schema = [
            'name' => 'mst_wards',
            'module' => 'Ward',
            'primary_key' => 'id',
            'endpoint' => '/wards',
            'scheme' => array_values($fields),
        ];

        return $this->sendSuccess($schema, 'Data berhasil ditampilkan.');
    }
}
