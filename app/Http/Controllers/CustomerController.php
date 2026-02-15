<?php

namespace App\Http\Controllers;

use App\Modules\Order\Models\Order;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Order::query()
            ->selectRaw('customer_email, customer_name, COUNT(*) as orders_count, SUM(total_amount) as total_spent, MAX(created_at) as last_order_at')
            ->whereNotNull('customer_email')
            ->groupBy('customer_email', 'customer_name')
            ->orderByDesc('last_order_at')
            ->paginate(10)
            ->withQueryString();

        $metrics = [
            'total_customers' => [
                'value' => $customers->total(),
            ],
            'total_revenue' => [
                'value' => Order::whereNotNull('customer_email')->sum('total_amount'),
            ],
        ];

        return Inertia::render('Customer/Index', [
            'customers' => $customers,
            'metrics' => $metrics,
        ]);
    }
}
