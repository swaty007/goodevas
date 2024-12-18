<?php

namespace App\Http\Controllers\CraftablePro;

use App\Exports\CraftablePro\OrdersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\CraftablePro\Order\BulkDestroyOrderRequest;
use App\Http\Requests\CraftablePro\Order\CreateOrderRequest;
use App\Http\Requests\CraftablePro\Order\DestroyOrderRequest;
use App\Http\Requests\CraftablePro\Order\EditOrderRequest;
use App\Http\Requests\CraftablePro\Order\IndexOrderRequest;
use App\Http\Requests\CraftablePro\Order\StoreOrderRequest;
use App\Http\Requests\CraftablePro\Order\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Order\OrderManager;
use App\Services\Order\OrderQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    public function __construct(
        protected OrderQueryService $orderQueryService,
        protected OrderManager $orderManager,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexOrderRequest $request): Response|JsonResponse
    {
        if ($request->wantsJson() && $request->get('bulk_select_all')) {
            return response()->json($this->orderQueryService->getPluckIndex());
        }

        $orders = $this->orderQueryService->getIndexPagination($request->get('per_page'));

        Session::put('orders_url', $request->fullUrl());

        return Inertia::render('Order/Index', [
            'orders' => $orders,
            'filtersOptions' => $this->orderQueryService->getFilterOptions(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateOrderRequest $request): Response
    {
        return Inertia::render('Order/Create', [
            // 'orderItemsOptions' => OrderItem::all()->map(fn ($model) => ['value' => $model->id, 'label' => $model->sku]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        dd($request->validated());
        $this->orderManager->store($request->validated());

        return redirect()->route('craftable-pro.orders.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EditOrderRequest $request, Order $order): Response
    {
        $order->load('items');

        return Inertia::render('Order/Edit', [
            'order' => $order,
            // 'orderItemsOptions' => OrderItem::all()->map(fn ($model) => ['value' => $model->id, 'label' => $model->sku]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        dd($request->validated());
        $this->orderManager->update($request->validated(), $order);

        if (session('orders_url')) {
            return redirect(session('orders_url'))->with(['message' => ___('craftable-pro', 'Operation successful')]);
        }

        return redirect()->route('craftable-pro.orders.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    public function bulkUpdate(UpdateOrderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $ids = $data['ids'];
        $orderData = Arr::except($data, ['ids']);
        $batchUuid = $this->orderManager->bulkUpdate($ids, $orderData);

        return redirect()->route('craftable-pro.orders.index')->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyOrderRequest $request, Order $order): RedirectResponse
    {
        dd('not now');
        $this->orderManager->delete($order);

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Bulk destroy resource.
     */
    public function bulkDestroy(BulkDestroyOrderRequest $request): RedirectResponse
    {
        dd('not now');
        // Mass delete of resource
        DB::transaction(function () use ($request) {
            collect($request->validated()['ids'])
                ->chunk(1000)
                ->each(function ($bulkChunk) {
                    Order::whereIn('id', $bulkChunk)->delete();
                });
        });

        // Individual delete of resource items
        //        DB::transaction(function () use ($request) {
        //            collect($request->validated()['ids'])->each(function ($id) {
        //                Order::find($id)->delete();
        //            });
        //        });

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Operation successful')]);
    }

    /**
     * Export
     */
    public function export(IndexOrderRequest $request): BinaryFileResponse
    {
        return Excel::download(new OrdersExport($request->all()), 'Orders-'.now()->format('dmYHi').'.xlsx');
    }
}
