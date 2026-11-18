<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $services = Service::all();

        return view('customer.index', compact('services'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'whatsapp_number' => ['required', 'regex:/^\+?[0-9]{8,15}$/'],
            'items' => 'required|array',
            'items.*' => 'nullable|integer|min:0|max:2147483647',
        ]);

        $items = array_filter($data['items'], fn ($quantity) => $quantity > 0);

        if (empty($items)) {
            return back()->withInput()->with('error', 'Pilih minimal satu layanan!');
        }

        DB::transaction(function () use ($data, $items) {
            $services = Service::whereKey(array_keys($items))->sharedLock()->get()->keyBy('id');

            if (array_diff_key($items, $services->all())) {
                throw ValidationException::withMessages(['items' => 'Ada layanan yang sudah tidak tersedia.']);
            }

            $order = Order::create([
                'customer_name' => $data['customer_name'],
                'whatsapp_number' => $data['whatsapp_number'],
                'total_price' => 0,
                'status' => 'Antrean',
            ]);

            $total = 0;

            foreach ($items as $serviceId => $quantity) {
                $subtotal = $services[$serviceId]->price_per_unit * $quantity;
                $total += $subtotal;

                if ($total > 2_147_483_647) {
                    throw ValidationException::withMessages(['items' => 'Jumlah pesanan terlalu besar.']);
                }

                $order->orderDetails()->create([
                    'service_id' => $serviceId,
                    'qty_or_weight' => $quantity,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total_price' => $total]);
        });

        return redirect()->route('customer.index')->with('success', 'Pesanan berhasil dibuat dan masuk antrean.');
    }

    public function adminDashboard(): View
    {
        $orders = Order::with('orderDetails.service')->latest()->get();

        return view('dashboard', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate(['status' => 'required|in:Antrean,Dicuci,Selesai']);
        $order->update($data);

        return back()->with('success', 'Status pesanan #'.$order->id.' berhasil diperbarui!');
    }
}
