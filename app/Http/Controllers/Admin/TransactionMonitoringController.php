<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionMonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['canteen', 'customer'])->latest();

        if ($canteenId = $request->input('canteen_id')) {
            $query->where('canteen_id', $canteenId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($method = $request->input('payment_method')) {
            $query->where('payment_method', $method);
        }

        if ($date = $request->input('date')) {
            $query->whereDate('created_at', $date);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();
        $canteens = Canteen::orderBy('name')->get();

        return view('admin.transactions.index', compact('orders', 'canteens'));
    }

    public function show(Order $order): View
    {
        $order->load(['canteen.seller', 'customer', 'items', 'payment']);

        return view('admin.transactions.show', compact('order'));
    }
}
