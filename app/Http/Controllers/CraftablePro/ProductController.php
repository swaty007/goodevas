<?php

namespace App\Http\Controllers\CraftablePro;

use App\Exports\CraftablePro\ProductsExport;
use App\Facades\YsellApiFacade;
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
use App\Utils\Paginate;
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
        $productsQuery = $this->getProductionQuery();

        if ($request->wantsJson() && $request->get('bulk_select_all')) {
            return response()->json($productsQuery->select(['id'])->pluck('id'));
        }

        $products = $this->getProductsFromQuery($productsQuery, $request->get('per_page') ?? 100);

        Session::put('products_url', $request->fullUrl());

        return Inertia::render('Product/Index', [
            'products' => $products,
            'warehouses' => Warehouse::all(),
        ]);
    }

    public function indexIncome(IndexProductRequest $request): Response|JsonResponse
    {
        //        dd($this->calculateStockChanges(3, '2021-01-01', '2024-11-14'));
        $productsQuery = $this->getProductionQuery();

        if ($request->wantsJson() && $request->get('bulk_select_all')) {
            return response()->json($productsQuery->select(['id'])->pluck('id'));
        }

        $products = $this->getProductsFromQuery($productsQuery, $request->get('per_page') ?? 100);

        Session::put('products_url', $request->fullUrl());

        return Inertia::render('Product/IndexIncome', [
            'products' => $products,
            'warehouses' => Warehouse::all(),
        ]);
    }

    public function indexApiProducts(IndexProductRequest $request): Response|JsonResponse
    {
        $productsApi = YsellApiFacade::getProductAllByAllYsellKeys();
        $productsApi = Paginate::defaultSort('isOpen')->paginate(
            $productsApi,
            $request->get('per_page', 100),
            $request->get('page', 1),
            $request->get('filter', [
                'search' => null,
            ]),
        );

        return Inertia::render('Product/IndexApi', [
            'productsApi' => $productsApi,
        ]);
    }

    private function getProductionQuery(): QueryBuilder
    {
        return QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id', 'ext_id', 'ean', 'additional_data', 'product_type_id'
                )),
            ])
            ->defaultSort('id')
            ->allowedSorts('id', 'ext_id', 'ean', 'additional_data', 'product_type_id');
    }

    private function getProductsFromQuery(QueryBuilder $query, $perPage = 50): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $query
            ->with([
                'type',
                'warehouses',
            ])
            ->select('id', 'ext_id', 'ean', 'additional_data', 'product_type_id')
            ->paginate($perPage)->withQueryString();
    }

    private function calculateStockChanges($productId, $startDate, $endDate)
    {
        $snapshots = StockSnapshot::where('product_id', $productId)
            ->whereBetween('snapshot_date', [$startDate, $endDate])
            ->orderBy('snapshot_date')
            ->get();

        $totalConsumption = 0;
        $totalRestock = 0;

        $snapshots->each(function (StockSnapshot $snapshot) use (&$totalConsumption, &$totalRestock) {
            $previousStock = array_sum($snapshot->warehouse_stock['stock'] ?? []);
            $previousIncome = array_sum($snapshot->warehouse_stock['income'] ?? []);
            $currentStock = array_sum($snapshot->warehouse_stock);
            $currentIncome = array_sum($snapshot->warehouse_stock);
            $differenceStock = $currentStock - $previousStock;
            $differenceIncome = $currentIncome - $previousIncome;

            if ($differenceStock < 0) {
                $totalConsumption += abs($differenceStock);
            } elseif ($differenceStock > 0) {
                $totalRestock += $differenceStock;
            }
        });

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
