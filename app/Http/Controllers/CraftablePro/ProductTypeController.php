<?php
namespace App\Http\Controllers\CraftablePro;

use App\Http\Controllers\Controller;
use App\Http\Requests\CraftablePro\ProductType\IndexProductTypeRequest;
use App\Http\Requests\CraftablePro\ProductType\CreateProductTypeRequest;
use App\Http\Requests\CraftablePro\ProductType\StoreProductTypeRequest;
use App\Http\Requests\CraftablePro\ProductType\EditProductTypeRequest;
use App\Http\Requests\CraftablePro\ProductType\UpdateProductTypeRequest;
use App\Http\Requests\CraftablePro\ProductType\DestroyProductTypeRequest;
use App\Http\Requests\CraftablePro\ProductType\BulkDestroyProductTypeRequest;
use App\Models\ProductType;
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
use App\Exports\CraftablePro\ProductTypesExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexProductTypeRequest $request): Response | JsonResponse
    {
        $productTypesQuery = QueryBuilder::for(ProductType::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id','name'
                )),
            ])
            ->defaultSort('id')
            ->allowedSorts('id','name');

        if ($request->wantsJson() && $request->get('bulk_select_all')) {
            return response()->json($productTypesQuery->select(['id'])->pluck('id'));
        }

        $productTypes = $productTypesQuery
            ->select('id','name')
            ->paginate($request->get('per_page'))->withQueryString();

        Session::put('productTypes_url', $request->fullUrl());

        return Inertia::render('ProductType/Index', [
            'productTypes' => $productTypes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateProductTypeRequest $request): Response
    {
        return Inertia::render('ProductType/Create', [
            
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductTypeRequest $request): RedirectResponse
    {
        $productType = ProductType::create($request->validated());

        return redirect()->route('craftable-pro.product-types.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EditProductTypeRequest $request, ProductType $productType): Response
    {
        return Inertia::render('ProductType/Edit', [
            'productType' => $productType,
            
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductTypeRequest $request, ProductType $productType): RedirectResponse
    {
        $productType->update($request->validated());

        if (session('productTypes_url')) {
            return redirect(session('productTypes_url'))->with(['message' => ___('craftable-pro', 'Operation successful')]);
        }

        return redirect()->route('craftable-pro.product-types.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyProductTypeRequest $request, ProductType $productType): RedirectResponse
    {
        $productType->delete();

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Bulk destroy resource.
     */
    public function bulkDestroy(BulkDestroyProductTypeRequest $request): RedirectResponse
    {
        // Mass delete of resource
        DB::transaction(function () use ($request) {
            collect($request->validated()['ids'])
                ->chunk(1000)
                ->each(function ($bulkChunk) {
                    ProductType::whereIn('id', $bulkChunk)->delete();
                });
        });

        // Individual delete of resource items
        //        DB::transaction(function () use ($request) {
        //            collect($request->validated()['ids'])->each(function ($id) {
        //                ProductType::find($id)->delete();
        //            });
        //        });

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Export
     */
    public function export(IndexProductTypeRequest $request): BinaryFileResponse
    {
        return Excel::download(new ProductTypesExport($request->all()), 'ProductTypes-'.now()->format("dmYHi").'.xlsx');
    }
}
