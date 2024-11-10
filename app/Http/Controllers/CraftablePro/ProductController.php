<?php

namespace App\Http\Controllers\CraftablePro;

use App\Exports\CraftablePro\ProductsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\CraftablePro\Product\BulkDestroyProductRequest;
use App\Http\Requests\CraftablePro\Product\CreateProductRequest;
use App\Http\Requests\CraftablePro\Product\DestroyProductRequest;
use App\Http\Requests\CraftablePro\Product\EditProductRequest;
use App\Http\Requests\CraftablePro\Product\IndexProductRequest;
use App\Http\Requests\CraftablePro\Product\StoreProductRequest;
use App\Http\Requests\CraftablePro\Product\UpdateProductIncomeRequest;
use App\Http\Requests\CraftablePro\Product\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\StockSnapshot;
use App\Models\Warehouse;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexProductRequest $request): Response|JsonResponse
    {
        $productsQuery = QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id', 'ext_id', 'ean', 'additional_data', 'product_type_id'
                )),
            ])
            ->defaultSort('id')
            ->allowedSorts('id', 'ext_id', 'ean', 'additional_data', 'product_type_id');

        if ($request->wantsJson() && $request->get('bulk_select_all')) {
            return response()->json($productsQuery->select(['id'])->pluck('id'));
        }

        $products = $productsQuery
            ->with([
                'type',
                'warehouses',
            ])
            ->select('id', 'ext_id', 'ean', 'additional_data', 'product_type_id')
            ->paginate($request->get('per_page'))->withQueryString();

        Session::put('products_url', $request->fullUrl());

        return Inertia::render('Product/Index', [
            'products' => $products,
            'warehouses' => Warehouse::all(),
        ]);
    }

    private function calculateStockChanges($productId, $startDate, $endDate)
    {
        $snapshots = StockSnapshot::where('product_id', $productId)
            ->whereBetween('snapshot_date', [$startDate, $endDate])
            ->orderBy('snapshot_date')
            ->get();

        $totalConsumption = 0;
        $totalRestock = 0;

        for ($i = 1; $i < $snapshots->count(); $i++) {
            $previousStock = array_sum($snapshots[$i - 1]->warehouse_stock);
            $currentStock = array_sum($snapshots[$i]->warehouse_stock);
            $difference = $currentStock - $previousStock;

            if ($difference < 0) {
                $totalConsumption += abs($difference);
            } elseif ($difference > 0) {
                $totalRestock += $difference;
            }
        }

        return [
            'total_consumption' => $totalConsumption,
            'total_restock' => $totalRestock,
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateProductRequest $request): Response
    {
        return Inertia::render('Product/Create', [
            'productTypes' => ProductType::all()->map(fn (ProductType $model) => [
                'value' => $model->id,
                'label' => $model->name,
            ]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated());

        return redirect()->route('craftable-pro.products.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EditProductRequest $request, Product $product): Response
    {
        return Inertia::render('Product/Edit', [
            'product' => $product,
            'productTypes' => ProductType::all()->map(fn (ProductType $model) => [
                'value' => $model->id,
                'label' => $model->name,
            ]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        if (session('products_url')) {
            return redirect(session('products_url'))->with(['message' => ___('craftable-pro', 'Operation successful')]);
        }

        return redirect()->route('craftable-pro.products.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    public function updateIncome(UpdateProductIncomeRequest $request, Product $product, Warehouse $warehouse): RedirectResponse
    {
        $product->warehouses()->syncWithoutDetaching([
            $warehouse->id => [
                'income_quantity' => $request->validated()['income_quantity'],
            ],
        ]);

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyProductRequest $request, Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Bulk destroy resource.
     */
    public function bulkDestroy(BulkDestroyProductRequest $request): RedirectResponse
    {
        // Mass delete of resource
        DB::transaction(function () use ($request) {
            collect($request->validated()['ids'])
                ->chunk(1000)
                ->each(function ($bulkChunk) {
                    Product::whereIn('id', $bulkChunk)->delete();
                });
        });

        // Individual delete of resource items
        //        DB::transaction(function () use ($request) {
        //            collect($request->validated()['ids'])->each(function ($id) {
        //                Product::find($id)->delete();
        //            });
        //        });

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Export
     */
    public function export(IndexProductRequest $request): BinaryFileResponse
    {
        return Excel::download(new ProductsExport($request->all()), 'Products-'.now()->format('dmYHi').'.xlsx');
    }
}
