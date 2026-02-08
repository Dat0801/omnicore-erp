<?php

namespace App\Modules\Order\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Order\Models\Order;
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
}
