<?php

namespace App\Modules\Purchasing\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Purchasing\Models\PurchaseOrder;
use App\Modules\Purchasing\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PurchaseOrderController extends Controller
{
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
}
