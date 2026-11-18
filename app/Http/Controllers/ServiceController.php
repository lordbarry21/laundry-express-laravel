<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::latest()->paginate(10);

        return view('admin.services.index', compact('services'));
    }

    public function create(): View
    {
        return view('admin.services.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service_name' => 'required|string|max:255',
            'category' => 'required|in:Kiloan,Satuan,Express',
            'price_per_unit' => 'required|integer|min:0|max:2147483647',
            'unit_type' => 'required|in:Kg,Pcs',
            'description' => 'required|string',
        ]);

        Service::create($data);

        return redirect()->route('services.index')->with('success', 'Data layanan berhasil ditambahkan!');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate([
            'service_name' => 'required|string|max:255',
            'category' => 'required|in:Kiloan,Satuan,Express',
            'price_per_unit' => 'required|integer|min:0|max:2147483647',
            'unit_type' => 'required|in:Kg,Pcs',
            'description' => 'required|string',
        ]);

        DB::transaction(function () use ($service, $data) {
            $service = Service::lockForUpdate()->findOrFail($service->id);

            if ($service->unit_type !== $data['unit_type'] && OrderDetail::where('service_id', $service->id)->exists()) {
                throw ValidationException::withMessages(['unit_type' => 'Satuan tidak bisa diubah karena layanan sudah pernah dipesan.']);
            }

            $service->update($data);
        });

        return redirect()->route('services.index')->with('success', 'Data layanan berhasil diperbarui!');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $deleted = DB::transaction(function () use ($service) {
            $service = Service::lockForUpdate()->findOrFail($service->id);

            if (OrderDetail::where('service_id', $service->id)->exists()) {
                return false;
            }

            return $service->delete();
        });

        if (! $deleted) {
            return back()->with('error', 'Layanan sudah dipakai di pesanan dan tidak bisa dihapus.');
        }

        return redirect()->route('services.index')->with('success', 'Data layanan berhasil dihapus!');
    }
}
