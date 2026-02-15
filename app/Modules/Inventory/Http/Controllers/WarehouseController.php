<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Inventory\Http\Requests\StoreWarehouseRequest;
use App\Modules\Inventory\Http\Requests\UpdateWarehouseRequest;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\StockMovement;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WarehouseController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Warehouse::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $stockValueSubquery = Inventory::query()
            ->selectRaw('warehouse_id, SUM(inventories.quantity * products.price) as stock_value')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->groupBy('warehouse_id');

        $warehouses = $query
            ->leftJoinSub($stockValueSubquery, 'stock_values', function ($join) {
                $join->on('warehouses.id', '=', 'stock_values.warehouse_id');
            })
            ->select('warehouses.*')
            ->selectRaw('COALESCE(stock_values.stock_value, 0) as stock_value')
            ->orderBy('warehouses.name')
            ->paginate(8)
            ->withQueryString();

        $totalWarehouses = Warehouse::count();
        $totalAssetValue = Inventory::query()
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->selectRaw('SUM(inventories.quantity * products.price) as total_value')
            ->value('total_value') ?? 0;

        $activePersonnel = User::where('is_active', true)->count();

        return Inertia::render('Warehouse/Index', [
            'warehouses' => $warehouses,
            'filters' => [
                'current' => $request->only(['search', 'status']),
            ],
            'stats' => [
                'totalWarehouses' => $totalWarehouses,
                'totalAssetValue' => $totalAssetValue,
                'activePersonnel' => $activePersonnel,
            ],
        ]);
    }

    public function show(Request $request, Warehouse $warehouse): Response
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();
        $categoryId = $request->integer('category_id');

        $inventoriesQuery = Inventory::query()
            ->where('warehouse_id', $warehouse->id)
            ->with(['product.category'])
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('product', function ($productQuery) use ($search) {
                    $productQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->whereHas('product', function ($productQuery) use ($categoryId) {
                    $productQuery->where('product_category_id', $categoryId);
                });
            })
            ->when($status !== '' && $status !== 'all', function ($query) use ($status) {
                if ($status === 'out_of_stock') {
                    $query->where('quantity', 0);
                }

                if ($status === 'low_stock') {
                    $query->whereColumn('quantity', '<=', 'reorder_level')->where('quantity', '>', 0);
                }

                if ($status === 'in_stock') {
                    $query->whereColumn('quantity', '>', 'reorder_level');
                }
            })
            ->orderByDesc('quantity');

        $inventories = $inventoriesQuery
            ->paginate(8)
            ->withQueryString();

        $totalSkus = Inventory::query()
            ->where('warehouse_id', $warehouse->id)
            ->distinct('product_id')
            ->count('product_id');

        $totalItems = Inventory::query()
            ->where('warehouse_id', $warehouse->id)
            ->sum('quantity');

        $stockValue = Inventory::query()
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->where('inventories.warehouse_id', $warehouse->id)
            ->selectRaw('SUM(inventories.quantity * products.price) as total_value')
            ->value('total_value') ?? 0;

        $inStockSkus = Inventory::query()
            ->where('warehouse_id', $warehouse->id)
            ->where('quantity', '>', 0)
            ->distinct('product_id')
            ->count('product_id');

        $utilization = $totalSkus > 0
            ? (int) round(($inStockSkus / $totalSkus) * 100)
            : 0;

        $incomingToday = StockMovement::query()
            ->where('type', 'in')
            ->whereDate('created_at', now())
            ->whereHas('inventory', function ($query) use ($warehouse) {
                $query->where('warehouse_id', $warehouse->id);
            })
            ->sum('quantity');

        $outgoingToday = StockMovement::query()
            ->where('type', 'out')
            ->whereDate('created_at', now())
            ->whereHas('inventory', function ($query) use ($warehouse) {
                $query->where('warehouse_id', $warehouse->id);
            })
            ->sum('quantity');

        $categories = ProductCategory::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Warehouse/Show', [
            'warehouse' => [
                'id' => $warehouse->id,
                'name' => $warehouse->name,
                'code' => $warehouse->code,
                'address' => $warehouse->address,
                'is_active' => $warehouse->is_active,
                'manager_name' => $warehouse->manager_name ?? null,
                'manager_email' => $warehouse->manager_email ?? null,
                'manager_phone' => $warehouse->manager_phone ?? null,
            ],
            'inventories' => $inventories,
            'filters' => [
                'current' => [
                    'search' => $search,
                    'status' => $status !== '' ? $status : 'all',
                    'category_id' => $categoryId,
                ],
                'categories' => $categories,
            ],
            'metrics' => [
                'totalSkus' => $totalSkus,
                'totalItems' => $totalItems,
                'stockValue' => $stockValue,
                'utilization' => $utilization,
                'incomingToday' => $incomingToday,
                'outgoingToday' => $outgoingToday,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Warehouse/Create');
    }

    public function store(StoreWarehouseRequest $request)
    {
        Warehouse::create($request->validated());

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse created successfully.');
    }

    public function edit(Warehouse $warehouse): Response
    {
        return Inertia::render('Warehouse/Edit', [
            'warehouse' => $warehouse,
        ]);
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse)
    {
        $warehouse->update($request->validated());

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->inventories()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete warehouse with existing inventory. Please clear inventory first.']);
        }

        $warehouse->delete();

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse deleted successfully.');
    }
}
