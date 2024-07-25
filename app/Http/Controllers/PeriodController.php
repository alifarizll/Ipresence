<?php

namespace App\Http\Controllers;

use App\Events\MasterDataChanged;
use App\Models\Period;
use App\Http\Requests\StorePeriodRequest;
use App\Http\Resources\PeriodResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeriodController extends Controller
{
    /**
     * @OA\Get(
     *      security={{"bearerAuth": {}}},
     *      path="/periods",
     *      tags={"Period"},
     *      summary="List of Period",
     *      @OA\Parameter(in="query", required=false, name="filter[name]", @OA\Schema(type="string"), example="keyword"),
     *      @OA\Parameter(in="query", required=false, name="filter[keyword]", @OA\Schema(type="string"), example="keyword"),
     *      @OA\Parameter(in="query", required=false, name="sort", @OA\Schema(type="string"), example="name"),
     *      @OA\Parameter(in="query", required=false, name="page", @OA\Schema(type="string"), example="1"),
     *      @OA\Parameter(in="query", required=false, name="rows", @OA\Schema(type="string"), example="10"),
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

        $periods = QueryBuilder::for(Period::class)
            ->allowedFilters([
                AllowedFilter::callback(
                    'keyword',
                    fn (Builder $query, $value) => $query->where('name', 'like', '%' . $value . '%')
                ),
                AllowedFilter::exact('id'),
                'name',
            ])
            ->allowedSorts('name', 'created_at')
            ->paginate($perPage)
            ->appends($request->query());

        return PeriodResource::collection($periods);
    }

    /**
     * @OA\Post(
     *      security={{"bearerAuth": {}}},
     *      path="/periods",
     *      tags={"Period"},
     *      summary="Store Period",
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="start_date", ref="#/components/schemas/Period/properties/start_date"),
*              @OA\Property(property="end_date", ref="#/components/schemas/Period/properties/end_date"),
*              @OA\Property(property="name", ref="#/components/schemas/Period/properties/name"),
*              @OA\Property(property="label", ref="#/components/schemas/Period/properties/label"),

     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Data sukses disimpan."),
     *          )
     *      ),
     *      @OA\Response(
     *          response="422",
     *          description="error",
     *          @OA\JsonContent(
    *              @OA\Property(property="start_date", type="array", @OA\Items(example={"start_date field is required."})),
*              @OA\Property(property="end_date", type="array", @OA\Items(example={"end_date field is required."})),
*              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
*              @OA\Property(property="label", type="array", @OA\Items(example={"label field is required."})),

     *          ),
     *      ),
     * )
     */
    public function store(StorePeriodRequest $request)
    {
        $user = auth('custom')->user();
        $operation = 'Menambah';
        $entityType = 'periode';
        $entityName = $request->name;
        $status = 'created';

        try {
            $period = period::create($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new PeriodResource($period), 'Data berhasil disimpan.', 201);
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to create period: '.$e->getMessage());

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
     *      path="/periods/{id}",
     *      tags={"Period"},
     *      summary="Period details",
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Period ID"),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function show(Period $period)
    {
        return $this->sendSuccess(new PeriodResource($period), 'Data berhasil ditampilkan.');
    }

    /**
     * @OA\Put(
     *      security={{"bearerAuth": {}}},
     *      path="/periods/{id}",
     *      tags={"Period"},
     *      summary="Update Period",
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Period ID"),
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="start_date", ref="#/components/schemas/Period/properties/start_date"),
*              @OA\Property(property="end_date", ref="#/components/schemas/Period/properties/end_date"),
*              @OA\Property(property="name", ref="#/components/schemas/Period/properties/name"),
*              @OA\Property(property="label", ref="#/components/schemas/Period/properties/label"),

     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Data sukses disimpan."),
     *          )
     *      ),
     *      @OA\Response(
     *          response="422",
     *          description="error",
     *          @OA\JsonContent(
    *              @OA\Property(property="start_date", type="array", @OA\Items(example={"start_date field is required."})),
*              @OA\Property(property="end_date", type="array", @OA\Items(example={"end_date field is required."})),
*              @OA\Property(property="name", type="array", @OA\Items(example={"name field is required."})),
*              @OA\Property(property="label", type="array", @OA\Items(example={"label field is required."})),

     *          ),
     *      ),
     * )
     */
    public function update(StorePeriodRequest $request, Period $period)
    {
        $user = auth('custom')->user();
        $operation = 'Mengubah';
        $entityType = 'periode';
        $entityName = $period->name;
        $status = 'updated';

        try {
            $period->update($request->all());

            event(new MasterDataChanged(
                $user,
                $entityType,
                $entityName,
                $operation,
                $status
            ));

            return $this->sendSuccess(new PeriodResource($period), 'Data sukses disimpan.');
        } catch (Exception $e) {
            $status = 'failed';
            Log::error('Failed to update period: '.$e->getMessage());

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
     *      path="/periods/{id}",
     *      tags={"Period"},
     *      summary="Period Removal",
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Period ID"),
     *      @OA\Response(
     *          response=204,
     *          description="Response success no content",
     *      ),
     * )
     */
    public function destroy(Period $period)
    {
        $user = auth('custom')->user();
        $operation = 'Menghapus';
        $entityType = 'periode';
        $entityName = $period->name;
        $status = 'deleted';

        try {
            $period->delete();

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
            Log::error('Failed to delete period: '.$e->getMessage());

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
     *      path="/periods/schema",
     *      tags={"Period"},
     *      summary="Schema of Period",
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function schema(Request $request)
    {
        $fields = DB::select('describe mst_periods');
        $schema = [
            'name' => 'mst_periods',
            'module' => 'Period',
            'primary_key' => 'id',
            'endpoint' => '/periods',
            'scheme' => array_values($fields),
        ];

        return $this->sendSuccess($schema, 'Data berhasil ditampilkan.');
    }
}
