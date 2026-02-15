<?php

namespace App\Modules\Purchasing\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Purchasing\Models\PurchaseOrder;
use App\Modules\Purchasing\Models\Supplier;
use App\Modules\Purchasing\Services\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PurchaseOrderController extends Controller
{
    public function __construct(
        protected PurchaseOrderService $service,
    ) {}

    public function index(Request $request)
    {
        $status = $request->string('status')->toString() ?: 'all';

        $query = PurchaseOrder::query()
            ->with(['supplier'])
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $purchaseOrders = $query->paginate(10)->withQueryString();

        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            $avgLead = DB::table('purchase_orders')
                ->whereNotNull('received_at')
                ->whereNotNull('ordered_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, ordered_at, received_at)) as hours')
                ->value('hours');
        } else {
            $avgLead = DB::table('purchase_orders')
                ->whereNotNull('received_at')
                ->whereNotNull('ordered_at')
                ->selectRaw('AVG((julianday(received_at) - julianday(ordered_at)) * 24) as hours')
                ->value('hours');
        }

        $metrics = [
            'pending' => PurchaseOrder::where('status', 'ordered')->count(),
            'totalValue' => (float) PurchaseOrder::sum('total_amount'),
            'avgLeadHours' => (float) ($avgLead ?? 0),
        ];

        return Inertia::render('PurchaseOrder/Index', [
            'purchaseOrders' => $purchaseOrders,
            'filters' => [
                'current' => [
                    'status' => $status,
                ],
            ],
            'metrics' => $metrics,
        ]);
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'supplier',
            'warehouse',
            'items.product',
        ]);

        $metrics = [
            'totalItems' => $purchaseOrder->items->count(),
            'totalQuantity' => (int) $purchaseOrder->items->sum('quantity'),
            'totalAmount' => (float) $purchaseOrder->total_amount,
        ];

        return Inertia::render('PurchaseOrder/Show', [
            'purchaseOrder' => $purchaseOrder,
            'metrics' => $metrics,
        ]);
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone', 'tax_id', 'address']);

        $warehouses = Warehouse::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return Inertia::render('PurchaseOrder/Create', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'expected_at' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $this->service->create($validated);

        return redirect()
            ->route('admin.purchase-orders.index')
            ->with('success', 'Purchase order created successfully.');
    }

    public function receive(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->service->receive($purchaseOrder);

        return redirect()
            ->route('admin.purchase-orders.index', ['status' => 'received'])
            ->with('success', 'Purchase order marked as received and inventory updated.');
    }
}
