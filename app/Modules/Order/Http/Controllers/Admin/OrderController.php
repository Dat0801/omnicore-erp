<?php

namespace App\Modules\Order\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Order\Http\Requests\UpdateOrderStatusRequest;
use App\Modules\Order\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->has('source') && $request->input('source') !== 'All Sources') {
            $query->where('source', $request->input('source'));
        }

        if ($request->has('status') && $request->input('status') !== 'All Statuses') {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('date_range')) {
            // Simple implementation for now, expecting array of dates or handled by frontend to send start/end
            // Skipping complex date range logic for MVP, can be added later
        }

        // Sort
        $query->latest();

        $orders = $query->paginate(10)->withQueryString();

        // Stats
        $stats = [
            'orders_today' => [
                'value' => Order::whereDate('created_at', today())->count(),
                'growth' => 12, // Mocked for now, or calculate if needed
            ],
            'pending_actions' => [
                'count' => Order::whereIn('status', ['pending', 'processing'])->count(),
                'amount' => Order::whereIn('status', ['pending', 'processing'])->sum('total_amount'),
            ],
            'revenue_today' => [
                'value' => Order::whereDate('created_at', today())->sum('total_amount'),
                'growth' => 8, // Mocked
            ],
        ];

        return Inertia::render('Order/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'source', 'status']),
            'stats' => $stats,
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'warehouse']);

        $transitions = $this->allowedTransitions();
        $nextStatuses = $transitions[$order->status] ?? [];

        return Inertia::render('Order/Show', [
            'order' => $order,
            'next_statuses' => $nextStatuses,
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $data = $request->validated();

        $transitions = $this->allowedTransitions();

        $current = $order->status;
        $next = $data['status'];

        if (! isset($transitions[$current]) || ! in_array($next, $transitions[$current], true)) {
            return back()->withErrors([
                'status' => 'Trạng thái không hợp lệ cho đơn hàng hiện tại.',
            ]);
        }

        $order->update(['status' => $next]);

        return redirect()
            ->route('admin.orders.show', $order->id)
            ->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }

    private function allowedTransitions(): array
    {
        return [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['picking', 'cancelled'],
            'picking' => ['packed', 'cancelled'],
            'packed' => ['shipped', 'cancelled'],
            'shipped' => ['delivered'],
            'delivered' => ['refunded'],
        ];
    }
}
