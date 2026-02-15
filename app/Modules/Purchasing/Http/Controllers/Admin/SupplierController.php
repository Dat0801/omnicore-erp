<?php

namespace App\Modules\Purchasing\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Purchasing\Models\PurchaseOrder;
use App\Modules\Purchasing\Models\PurchaseOrderItem;
use App\Modules\Purchasing\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $suppliers = $query->latest()->paginate(10)->withQueryString();

        $totalSuppliers = Supplier::count();
        $activeSuppliers = Supplier::where('is_active', true)->count();
        $totalPurchaseOrders = PurchaseOrder::count();

        return Inertia::render('Supplier/Index', [
            'suppliers' => $suppliers,
            'filters' => [
                'current' => $request->only(['search', 'status']),
            ],
            'stats' => [
                'total' => $totalSuppliers,
                'active' => $activeSuppliers,
                'poCount' => $totalPurchaseOrders,
            ],
        ]);
    }

    public function show(Supplier $supplier, Request $request)
    {
        $supplier->loadCount([
            'purchaseOrders as po_count',
        ]);

        $productStats = PurchaseOrderItem::query()
            ->selectRaw('COUNT(DISTINCT purchase_order_items.product_id) AS total_skus')
            ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->where('purchase_orders.supplier_id', $supplier->id)
            ->first();

        $avgCost = PurchaseOrderItem::query()
            ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->where('purchase_orders.supplier_id', $supplier->id)
            ->avg('purchase_order_items.price') ?? 0;

        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            $leadTimeDays = PurchaseOrder::query()
                ->where('supplier_id', $supplier->id)
                ->whereNotNull('received_at')
                ->whereNotNull('ordered_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(DAY, ordered_at, received_at)) as avg_days')
                ->value('avg_days');
        } else {
            $leadTimeDays = PurchaseOrder::query()
                ->where('supplier_id', $supplier->id)
                ->whereNotNull('received_at')
                ->whereNotNull('ordered_at')
                ->selectRaw('AVG((julianday(received_at) - julianday(ordered_at))) as avg_days')
                ->value('avg_days');
        }

        $leadTimeDays = $leadTimeDays ? round((float) $leadTimeDays) : null;

        $search = $request->string('search')->toString();

        $products = PurchaseOrderItem::query()
            ->select([
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('AVG(purchase_order_items.price) AS unit_price'),
                DB::raw('MAX(purchase_orders.ordered_at) AS last_ordered_at'),
            ])
            ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('products', 'purchase_order_items.product_id', '=', 'products.id')
            ->where('purchase_orders.supplier_id', $supplier->id)
            ->when($search, function ($q) use ($search) {
                $q->where('products.name', 'like', "%{$search}%")
                    ->orWhere('products.sku', 'like', "%{$search}%");
            })
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc(DB::raw('MAX(purchase_orders.ordered_at)'))
            ->paginate(10)
            ->withQueryString();

        $productIds = collect($products->items())->pluck('id')->all();
        $stockMap = Inventory::query()
            ->whereIn('product_id', $productIds)
            ->select('product_id', DB::raw('SUM(quantity) as stock_qty'))
            ->groupBy('product_id')
            ->pluck('stock_qty', 'product_id');

        $products->getCollection()->transform(function ($row) use ($stockMap) {
            $row->stock_qty = (int) ($stockMap[$row->id] ?? 0);

            return $row;
        });

        $onTimeRate = PurchaseOrder::query()
            ->where('supplier_id', $supplier->id)
            ->whereNotNull('expected_at')
            ->whereNotNull('received_at')
            ->selectRaw('AVG(received_at <= expected_at) AS rate')
            ->value('rate');

        $performance = $onTimeRate !== null ? round($onTimeRate * 5, 1) : null;

        return Inertia::render('Supplier/Show', [
            'supplier' => $supplier,
            'metrics' => [
                'totalSkus' => (int) ($productStats->total_skus ?? 0),
                'avgCost' => (float) $avgCost,
                'leadTimeDays' => $leadTimeDays,
                'rating' => $performance,
            ],
            'products' => $products,
            'filters' => [
                'current' => $request->only(['search']),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Supplier/Create');
    }
}
