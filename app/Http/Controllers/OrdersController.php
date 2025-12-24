<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    public function index(Request $request)
    {
        $userId = auth()->id();
        $status = $request->query('status', 'all'); 

        $stats = [
            'total' => Order::where('buyer_id', $userId)->count(),
            'pending_payment' => Order::where('buyer_id', $userId)
                ->where('payment_status', 'pending')
                ->where('status', '!=', 'cancelled')
                ->count(),
            'processed' => Order::where('buyer_id', $userId)
                ->where('status', 'processed')
                ->count(),
            'shipped' => Order::where('buyer_id', $userId)
                ->where('status', 'shipped')
                ->count(),
            'completed' => Order::where('buyer_id', $userId)
                ->where('status', 'completed')
                ->count(),
        ];
        
        $query = Order::where('buyer_id', $userId)->with(['items.product.currentUserReview', 'seller.profile'])->latest();

        switch ($status) {
            case 'pending':
                $query->where('payment_status', 'pending')->where('status', '!=', 'cancelled');
                break;
            case 'processed': 
                $query->where('status', 'processed');
                break;
            case 'shipped': 
                $query->where('status', 'shipped');
                break;
            case 'completed': 
                $query->where('status', 'completed');
                break;
            case 'cancelled': 
                $query->where('status', 'cancelled');
                break;
            case 'all':
            default:
                break;
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('orders.index', compact('orders', 'stats', 'status'));
    }

    public function show(Order $order)
    {
        $isBuyer = (int)$order->buyer_id === (int)auth()->id();
        $isAdmin = auth()->user() && auth()->user()->role === 'admin';

        if (!$isBuyer && !$isAdmin) {
            abort(403);
        }

        $order->load(['items.product.currentUserReview', 'seller.profile', 'shippingAddress', 'buyer', 'shipment.carrier']);
        
        return view('orders.show', compact('order'));
    }

    public function pay($id)
    {
        $order = Order::where('buyer_id', auth()->id())
            ->where('order_id', $id)
            ->firstOrFail();

        if ($order->payment_status !== 'pending') {
             return response()->json(['status' => 'error', 'message' => 'Pesanan sudah dibayar.'], 400);
        }

        $payment = \App\Models\Payment::where('order_id', $order->order_id)
            ->whereIn('status', ['pending', 'challenge'])
            ->latest()
            ->first();

        if (!$payment && preg_match('/Group Payment: (PAY-[\w\-]+)/', $order->notes, $matches)) {
            $groupCode = $matches[1];
            $payment = \App\Models\Payment::where('provider_order_id', $groupCode)
                ->whereIn('status', ['pending', 'challenge'])
                ->latest() 
                ->first();
        }

        if ($payment) {
            return response()->json([
                'status' => 'success',
                'redirect_url' => route('payment.show', $payment->payment_id)
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Data pembayaran tidak ditemukan. Silakan hubungi bantuan.'], 404);
    }
}