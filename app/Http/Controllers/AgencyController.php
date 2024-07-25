<?php

namespace App\Http\Controllers;

use App\Events\MasterDataChanged;
use App\Http\Requests\StoreAgencyRequest;
use App\Http\Resources\AgencyResource;
use App\Models\Agency;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AgencyController extends Controller
{
    /**
     * @OA\Get(
     *      security={{"bearerAuth": {}}},
     *      path="/agencies",
     *      tags={"Agency"},
     *      summary="List of Agency",
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

        $agencies = QueryBuilder::for(Agency::class)
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

        return AgencyResource::collection($agencies);
    }

    /**
     * @OA\Post(
     *      security={{"bearerAuth": {}}},
     *      path="/agencies",
     *      tags={"Agency"},
     *      summary="Store Agency",
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="level_id", ref="#/components/schemas/Agency/properties/level_id"),
     *              @OA\Property(property="name", ref="#/components/schemas/Agency/properties/name"),
     *              @OA\Property(property="acronym", ref="#/components/schemas/Agency/properties/acronym"),
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
     *              @OA\Property(property="level_id", type="array", @OA\Items(example={"level_id field is required."})),
     *              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
     *              @OA\Property(property="acronym", type="array", @OA\Items(example={"acronym field is required."})),
     *          ),
     *      ),
     * )
     */
    public function store(StoreAgencyRequest $request)
    {
        $user = auth('custom')->user();
        $operation = 'Menambah';
        $entityType = 'agen';
        $entityName = $request->name;
        $status = 'created';

        try {
            $agency = Agency::create($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new AgencyResource($agency), 'Data berhasil disimpan.', 201);
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to create agency: '.$e->getMessage());

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
     *      path="/agencies/{id}",
     *      tags={"Agency"},
     *      summary="Agency details",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Agency ID"),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function show(Agency $agency)
    {
        return $this->sendSuccess(new AgencyResource($agency), 'Data berhasil ditampilkan.');
    }

    /**
     * @OA\Put(
     *      security={{"bearerAuth": {}}},
     *      path="/agencies/{id}",
     *      tags={"Agency"},
     *      summary="Update Agency",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Agency ID"),
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="level_id", ref="#/components/schemas/Agency/properties/level_id"),
     *              @OA\Property(property="name", ref="#/components/schemas/Agency/properties/name"),
     *              @OA\Property(property="acronym", ref="#/components/schemas/Agency/properties/acronym"),
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
     *              @OA\Property(property="level_id", type="array", @OA\Items(example={"level_id field is required."})),
     *              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
     *              @OA\Property(property="acronym", type="array", @OA\Items(example={"acronym field is required."})),
     *          ),
     *      ),
     * )
     */
    public function update(StoreAgencyRequest $request, Agency $agency)
    {
        $user = auth('custom')->user();
        $operation = 'Mengubah';
        $entityType = 'agen';
        $entityName = $agency->name;
        $status = 'updated';

        try {
            $agency->update($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new AgencyResource($agency), 'Data sukses disimpan.');
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to update agency: '.$e->getMessage());

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
     *      path="/agencies/{id}",
     *      tags={"Agency"},
     *      summary="Agency Removal",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Agency ID"),
     *
     *      @OA\Response(
     *          response=204,
     *          description="Response success no content",
     *      ),
     * )
     */
    public function destroy(Agency $agency)
    {
        $user = auth('custom')->user();
        $operation = 'Menghapus';
        $entityType = 'agen';
        $entityName = $agency->name;
        $status = 'deleted';

        try {
            $agency->delete();

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
            Log::error('Failed to delete agency: '.$e->getMessage());

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
     *      path="/agencies/schema",
     *      tags={"Agency"},
     *      summary="Schema of Agency",
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function schema(Request $request)
    {
        $fields = DB::select('describe mst_agencies');
        $schema = [
            'name' => 'mst_agencies',
            'module' => 'Agency',
            'primary_key' => 'id',
            'endpoint' => '/agencies',
            'scheme' => array_values($fields),
        ];

        return $this->sendSuccess($schema, 'Data berhasil ditampilkan.');
    }
}
