<?php

namespace App\Http\Controllers;

use App\Events\MasterDataChanged;
use App\Http\Requests\StoreRegencyRequest;
use App\Http\Resources\RegencyResource;
use App\Models\Regency;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RegencyController extends Controller
{
    /**
     * @OA\Get(
     *      security={{"bearerAuth": {}}},
     *      path="/regencies",
     *      tags={"Regency"},
     *      summary="List of Regency",
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

        $regencies = QueryBuilder::for(Regency::class)
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

        return RegencyResource::collection($regencies);
    }

    /**
     * @OA\Post(
     *      security={{"bearerAuth": {}}},
     *      path="/regencies",
     *      tags={"Regency"},
     *      summary="Store Regency",
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="province_id", ref="#/components/schemas/Regency/properties/province_id"),
     *              @OA\Property(property="land_area", ref="#/components/schemas/Regency/properties/land_area"),
     *              @OA\Property(property="code", ref="#/components/schemas/Regency/properties/code"),
     *              @OA\Property(property="name", ref="#/components/schemas/Regency/properties/name"),
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
     *              @OA\Property(property="land_area", type="array", @OA\Items(example={"land_area field is required."})),
     *              @OA\Property(property="code", type="array", @OA\Items(example={"code field is required."})),
     *              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
     *          ),
     *      ),
     * )
     */
    public function store(StoreRegencyRequest $request)
    {
        $user = auth('custom')->user();
        $operation = 'Menambah';
        $entityType = 'kab/kota';
        $entityName = $request->name;
        $status = 'created';

        try {
            $regency = Regency::create($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new RegencyResource($regency), 'Data berhasil disimpan.', 201);
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to create regency: '.$e->getMessage());

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
     *      path="/regencies/{id}",
     *      tags={"Regency"},
     *      summary="Regency details",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Regency ID"),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function show(Regency $regency)
    {
        return $this->sendSuccess(new RegencyResource($regency), 'Data berhasil ditampilkan.');
    }

    /**
     * @OA\Put(
     *      security={{"bearerAuth": {}}},
     *      path="/regencies/{id}",
     *      tags={"Regency"},
     *      summary="Update Regency",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Regency ID"),
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="province_id", ref="#/components/schemas/Regency/properties/province_id"),
     *              @OA\Property(property="land_area", ref="#/components/schemas/Regency/properties/land_area"),
     *              @OA\Property(property="code", ref="#/components/schemas/Regency/properties/code"),
     *              @OA\Property(property="name", ref="#/components/schemas/Regency/properties/name"),
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
     *              @OA\Property(property="land_area", type="array", @OA\Items(example={"land_area field is required."})),
     *              @OA\Property(property="code", type="array", @OA\Items(example={"code field is required."})),
     *              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
     *          ),
     *      ),
     * )
     */
    public function update(StoreRegencyRequest $request, Regency $regency)
    {
        $user = auth('custom')->user();
        $operation = 'Mengubah';
        $entityType = 'kab/kota';
        $entityName = $regency->name;
        $status = 'updated';

        try {
            $regency->update($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new RegencyResource($regency), 'Data sukses disimpan.');
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to update regency: '.$e->getMessage());

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
     *      path="/regencies/{id}",
     *      tags={"Regency"},
     *      summary="Regency Removal",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Regency ID"),
     *
     *      @OA\Response(
     *          response=204,
     *          description="Response success no content",
     *      ),
     * )
     */
    public function destroy(Regency $regency)
    {
        $user = auth('custom')->user();
        $operation = 'Menghapus';
        $entityType = 'kab/kota';
        $entityName = $regency->name;
        $status = 'deleted';

        try {
            $regency->delete();

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
            Log::error('Failed to delete regency: '.$e->getMessage());

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
     *      path="/regencies/schema",
     *      tags={"Regency"},
     *      summary="Schema of Regency",
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function schema(Request $request)
    {
        $fields = DB::select('describe mst_regencies');
        $schema = [
            'name' => 'mst_regencies',
            'module' => 'Regency',
            'primary_key' => 'id',
            'endpoint' => '/regencies',
            'scheme' => array_values($fields),
        ];

        return $this->sendSuccess($schema, 'Data berhasil ditampilkan.');
    }
}
