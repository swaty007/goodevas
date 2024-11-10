<?php
namespace App\Http\Controllers\CraftablePro;

use App\Http\Controllers\Controller;
use App\Http\Requests\CraftablePro\Warehouse\IndexWarehouseRequest;
use App\Http\Requests\CraftablePro\Warehouse\CreateWarehouseRequest;
use App\Http\Requests\CraftablePro\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\CraftablePro\Warehouse\EditWarehouseRequest;
use App\Http\Requests\CraftablePro\Warehouse\UpdateWarehouseRequest;
use App\Http\Requests\CraftablePro\Warehouse\DestroyWarehouseRequest;
use App\Http\Requests\CraftablePro\Warehouse\BulkDestroyWarehouseRequest;
use App\Models\Warehouse;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CraftablePro\WarehousesExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexWarehouseRequest $request): Response | JsonResponse
    {
        $warehousesQuery = QueryBuilder::for(Warehouse::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id','name','country_id'
                )),
            ])
            ->defaultSort('id')
            ->allowedSorts('id','name','country_id');

        if ($request->wantsJson() && $request->get('bulk_select_all')) {
            return response()->json($warehousesQuery->select(['id'])->pluck('id'));
        }

        $warehouses = $warehousesQuery
            ->select('id','name','country_id')
            ->paginate($request->get('per_page'))->withQueryString();

        Session::put('warehouses_url', $request->fullUrl());

        return Inertia::render('Warehouse/Index', [
            'warehouses' => $warehouses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateWarehouseRequest $request): Response
    {
        return Inertia::render('Warehouse/Create', [
            
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWarehouseRequest $request): RedirectResponse
    {
        $warehouse = Warehouse::create($request->validated());

        return redirect()->route('craftable-pro.warehouses.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EditWarehouseRequest $request, Warehouse $warehouse): Response
    {
        return Inertia::render('Warehouse/Edit', [
            'warehouse' => $warehouse,
            
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse): RedirectResponse
    {
        $warehouse->update($request->validated());

        if (session('warehouses_url')) {
            return redirect(session('warehouses_url'))->with(['message' => ___('craftable-pro', 'Operation successful')]);
        }

        return redirect()->route('craftable-pro.warehouses.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyWarehouseRequest $request, Warehouse $warehouse): RedirectResponse
    {
        $warehouse->delete();

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Bulk destroy resource.
     */
    public function bulkDestroy(BulkDestroyWarehouseRequest $request): RedirectResponse
    {
        // Mass delete of resource
        DB::transaction(function () use ($request) {
            collect($request->validated()['ids'])
                ->chunk(1000)
                ->each(function ($bulkChunk) {
                    Warehouse::whereIn('id', $bulkChunk)->delete();
                });
        });

        // Individual delete of resource items
        //        DB::transaction(function () use ($request) {
        //            collect($request->validated()['ids'])->each(function ($id) {
        //                Warehouse::find($id)->delete();
        //            });
        //        });

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Export
     */
    public function export(IndexWarehouseRequest $request): BinaryFileResponse
    {
        return Excel::download(new WarehousesExport($request->all()), 'Warehouses-'.now()->format("dmYHi").'.xlsx');
    }
}
