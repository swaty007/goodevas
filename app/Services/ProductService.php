<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\ProductIncome;
use App\Models\StockSnapshot;
use App\Models\Warehouse;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductService
{
    public function getProductionQuery(): QueryBuilder
    {
        return QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id',
                    'ext_id',
                    'ean',
                    'additional_data',
                    'product_type_id',
                )),
            ])
            ->defaultSort('id')
            ->allowedSorts('id', 'ext_id', 'ean', 'additional_data', 'product_type_id');
    }

    public function getProductsFromQuery(QueryBuilder $query, $perPage = 50): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $products = $query
            ->with([
                'type',
                'warehouses',
                'incomes', //.warehouse
            ])
            ->select(
                'id',
                'ext_id',
                'ean',
                'additional_data',
                'product_type_id',
            )
            ->paginate($perPage)->withQueryString();

        $days = Paginator::resolveQueryString()['filter']['days'] ?? 6;
        $products->getCollection()->each(function (Product $product) use ($days) {
            $product->stock_changes = $this->calculateStockChanges($product->id, (int) $days);
            $product->incomes; //->groupBy('warehouse.ysell_name')
        });

        return $products;
    }

    public function calculateStockChanges(int|string $productId, int $days): array
    {
        return Cache::remember('stock_changes_'.$productId.'_'.$days, now()->addMinutes(5), function () use ($productId, $days) {
            $snapshots = StockSnapshot::where('product_id', $productId)
                ->where('snapshot_date', '>=', now()->subDays($days))
                ->orderBy('snapshot_date')
                ->get();

            $totalConsumption = [];
            $totalRestock = [];

            $previousStocks = [];
            $previousIncomes = null;
            $snapshots->each(function (StockSnapshot $snapshot) use (&$totalConsumption, &$totalRestock, &$previousStocks, &$previousIncomes) {
                $stocks = $snapshot->warehouse_stock['stock'];
                foreach ($stocks as $key => $stock) {
                    if (! isset($previousStocks[$key])) {
                        $previousStocks[$key] = $stock;
                    }
                    if (! isset($totalConsumption[$key])) {
                        $totalConsumption[$key] = 0;
                    }
                    if (! isset($totalRestock[$key])) {
                        $totalRestock[$key] = 0;
                    }

                    $currentStock = $stock;

                    $differenceStock = $stock - $previousStocks[$key];

                    if ($differenceStock < 0) {
                        $totalConsumption[$key] += abs($differenceStock);
                    } elseif ($differenceStock > 0) {
                        $totalRestock[$key] += $differenceStock;
                    }
                    $previousStocks[$key] = $currentStock;
                }

                $incomes = $snapshot->warehouse_stock['income'];
                $currentIncome = array_sum($incomes);
                $differenceIncome = $currentIncome - $previousIncomes;
                if (is_null($previousIncomes)) {
                    $previousIncomes = array_sum($incomes);
                }
                $previousIncomes = $currentIncome;
            });

            return [
                'total_consumption' => $totalConsumption,
                'total_restock' => $totalRestock,
            ];
        });
    }

    protected Collection $warehouses;

    public function getWarehouses(): Collection
    {
        if (empty($this->warehouses)) {
            $this->warehouses = Cache::remember('warehouses', now()->addMinutes(5), function () {
                //                return Warehouse::with(['futureIncomesDates'])->get()->keyBy('ysell_name');
                return Warehouse::all()->keyBy('ysell_name');
            });
            $incomeDates = $this->getFutureIncomeDatesByWarehouse();
            $this->warehouses->each(function (Warehouse $warehouse) use ($incomeDates) {
                $warehouse->futureIncomesDates = $incomeDates[$warehouse->ysell_name] ?? [
                    now()->addDays(1)->format('Y-m-d'),
                ];
            });
        }

        return $this->warehouses;
    }

    public function getFutureIncomeDatesByWarehouse()
    {
        // Получаем текущую дату
        $currentDate = now()->subDays(2)->format('Y-m-d');

        // Получаем уникальные будущие даты приходов, сгруппированные по складам
        $incomes = ProductIncome::select(['warehouse_id', 'income_date'])
            ->with('warehouse')
            ->where('income_date', '>=', $currentDate)  // Только будущие даты
            ->where('quantity', '>', 0) // Только положительные приходы
            ->distinct()
            ->orderBy('income_date', 'asc')
            ->get()
            ->groupBy('warehouse.ysell_name')
            ->map(function ($item) {
                return $item->pluck('income_date');
            })
            ->toArray();

        return $incomes;
    }
}
