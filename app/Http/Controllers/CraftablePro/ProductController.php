<?php

namespace App\Http\Controllers\CraftablePro;

use App\Exports\CraftablePro\ProductsExport;
use App\Exports\CraftablePro\ProductsIncomeExport;
use App\Facades\YsellApiFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\CraftablePro\Product\BulkDestroyProductRequest;
use App\Http\Requests\CraftablePro\Product\CreateProductRequest;
use App\Http\Requests\CraftablePro\Product\DestroyProductRequest;
use App\Http\Requests\CraftablePro\Product\EditProductRequest;
use App\Http\Requests\CraftablePro\Product\ImportProductIncomeRequest;
use App\Http\Requests\CraftablePro\Product\IndexProductRequest;
use App\Http\Requests\CraftablePro\Product\MoveProductIncomeRequest;
use App\Http\Requests\CraftablePro\Product\StoreProductRequest;
use App\Http\Requests\CraftablePro\Product\UpdateProductIncomeRequest;
use App\Http\Requests\CraftablePro\Product\UpdateProductRequest;
use App\Imports\ProductsIncomeImport;
use App\Models\Product;
use App\Models\ProductIncome;
use App\Models\ProductType;
use App\Models\Warehouse;
use App\Services\ProductService;
use App\Utils\Paginate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexProductRequest $request): Response|JsonResponse
    {
        $productsQuery = $this->productService->getProductionQuery();

        if ($request->wantsJson() && $request->get('bulk_select_all')) {
            return response()->json($productsQuery->select(['id'])->pluck('id'));
        }

        $products = $this->productService->getProductsFromQuery($productsQuery, $request->get('per_page') ?? 100);

        Session::put('products_url', $request->fullUrl());

        return Inertia::render('Product/Index', [
            'products' => $products,
            'warehouses' => $this->productService->getWarehouses(),
        ]);
    }

    public function indexIncome(IndexProductRequest $request): Response|JsonResponse
    {
        $productsQuery = $this->productService->getProductionQuery();

        if ($request->wantsJson() && $request->get('bulk_select_all')) {
            return response()->json($productsQuery->select(['id'])->pluck('id'));
        }

        $products = $this->productService->getProductsFromQuery($productsQuery, $request->get('per_page') ?? 100);

        Session::put('products_url_income', $request->fullUrl());

        return Inertia::render('Product/IndexIncome', [
            'products' => $products,
            'warehouses' => $this->productService->getWarehouses(),
            // 'futureIncomeDates' => $this->productService->getFutureIncomeDatesByWarehouse(),
        ]);
    }

    public function indexForecast(IndexProductRequest $request): Response|JsonResponse
    {
        $productsQuery = $this->productService->getProductionQuery();

        if ($request->wantsJson() && $request->get('bulk_select_all')) {
            return response()->json($productsQuery->select(['id'])->pluck('id'));
        }

        $products = $this->productService->getProductsFromQuery($productsQuery, $request->get('per_page') ?? 100);

        Session::put('products_url_income', $request->fullUrl());

        return Inertia::render('Product/IndexForecast', [
            'products' => $products,
            'warehouses' => $this->productService->getWarehouses(),
            // 'futureIncomeDates' => $this->productService->getFutureIncomeDatesByWarehouse(),
        ]);
    }

    public function indexApiProducts(IndexProductRequest $request): Response|JsonResponse
    {
        $productsApiData = YsellApiFacade::getProductAllByAllYsellKeys();
        $productsApiData = collect($productsApiData)->filter(fn ($item) => $item['condition'] !== 'New');
        $productsApi = Paginate::defaultSort('isOpen')->paginate(
            $productsApiData,
            $request->get('per_page', 100),
            $request->get('page', 1),
            $request->get('filter', [
                'search' => null,
            ]),
        );

        return Inertia::render('Product/IndexApi', [
            'productsApi' => $productsApi,
            'productsOptions' => [
                'condition' => collect($productsApiData)->pluck('condition')->unique()->values(),
            ],
            'warehouses' => $this->productService->getWarehouses(),
        ]);
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
        //        $product->warehouses()->syncWithoutDetaching([
        //            $warehouse->id => [
        //                'income_quantity' => $request->validated()['income_quantity'],
        //            ],
        //        ]);
        $data = $request->validated();
        ProductIncome::updateOrCreate([
            'product_id' => $product->id,
            'warehouse_id' => (int) $warehouse->id,
            'income_date' => $data['income_date'],
        ], [
            'quantity' => $data['income_quantity'],
        ]);

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    public function moveIncomes(MoveProductIncomeRequest $request, Warehouse $warehouse): RedirectResponse
    {
        $data = $request->validated();
        ProductIncome::where(['income_date' => $data['date_from'], 'warehouse_id' => $warehouse->id])
            ->update([
                'income_date' => $data['income_date'],
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
        $format = 'xlsx';
        if ($request->get('format')) {
            $format = $request->get('format');
        }
        return Excel::download(new ProductsExport($request->all()), 'Products-'.now()->format('dmYHi').".$format");
    }

    public function exportIncome(IndexProductRequest $request): BinaryFileResponse
    {
        $format = 'csv';
        if ($request->get('format')) {
            $format = $request->get('format');
        }
        return Excel::download(new ProductsIncomeExport($request->all()), 'Products-income-'.now()->format('dmYHi').".$format");
    }

    public function importIncome(ImportProductIncomeRequest $request): RedirectResponse
    {
        $file = $request->validated()['file'];
        //        Excel::import(new UsersImport, $file);
        $import = new ProductsIncomeImport;
        $import->import($file);

        return redirect()
            ->route('craftable-pro.products.index-income')
            ->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }
}
