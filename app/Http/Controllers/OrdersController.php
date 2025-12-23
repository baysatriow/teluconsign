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

    /**
     * Tampilkan Daftar Pesanan dengan Statistik
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $status = $request->query('status', 'all'); // all, pending, processed, shipped, completed, cancelled

        // Calculate Statistics (Keep existing logic)
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
        
        // Base Query
        $query = Order::where('buyer_id', $userId)->with(['items.product', 'seller'])->latest();

        // Apply Status Filter
        switch ($status) {
            case 'pending':
                $query->where('payment_status', 'pending')->where('status', '!=', 'cancelled');
                break;
            case 'processed': // Dikemas
                $query->where('status', 'processed');
                break;
            case 'shipped': // Dikirim
                $query->where('status', 'shipped');
                break;
            case 'completed': // Selesai
                $query->where('status', 'completed');
                break;
            case 'cancelled': // Dibatalkan
                $query->where('status', 'cancelled');
                break;
            case 'all':
            default:
                // No specific filter
                break;
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('orders.index', compact('orders', 'stats', 'status'));
    }

    /**
     * Tampilkan Detail Pesanan
     */
    public function show(Order $order)
    {
        // Ensure strictly buyer's order
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.product', 'seller', 'shippingAddress', 'buyer']);
        
        return view('orders.show', compact('order'));
    }

    /**
     * Bayar Pesanan (Individual)
     * Regenerate Snap Token untuk order spesifik
     */
    public function pay($id)
    {
        $order = Order::where('buyer_id', auth()->id())
            ->where('order_id', $id)
            ->firstOrFail();

        if ($order->payment_status !== 'pending') {
             return response()->json(['status' => 'error', 'message' => 'Pesanan sudah dibayar.'], 400);
        }

        // 1. Cek Payment Record via Direct Relation (jika order ini induk/single)
        $payment = \App\Models\Payment::where('order_id', $order->order_id)
            ->whereIn('status', ['pending', 'challenge'])
            ->latest()
            ->first();

        // 2. Cek via Group Code di Notes (jika order ini bagian dari group checkout)
        if (!$payment && preg_match('/Group Payment: (PAY-[\w\-]+)/', $order->notes, $matches)) {
            $groupCode = $matches[1];
            $payment = \App\Models\Payment::where('provider_order_id', $groupCode)
                ->whereIn('status', ['pending', 'challenge'])
                ->latest() // Ambil yang paling baru jika ada retry
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
