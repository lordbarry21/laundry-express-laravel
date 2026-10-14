<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Menampilkan daftar seluruh master layanan laundry (Halaman Index CRUD)
     */
    public function index()
    {
        $services = Service::latest()->paginate(10);
        return view('services.index', compact('services'));
    }

    /**
     * Menampilkan halaman formulir tambah layanan baru (Halaman Create Tersendiri)
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * Menyimpan data layanan baru ke basis data
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name'   => 'required|string|max:255',
            'category'       => 'required|in:Kiloan,Satuan,Express',
            'price_per_unit' => 'required|integer|min:0',
            'unit_type'      => 'required|in:Kg,Pcs',
            'description'    => 'nullable|string',
        ]);

        Service::create($validated);

        return redirect()->route('services.index')->with('success', 'Paket layanan laundry baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan halaman formulir edit layanan (Halaman Edit Tersendiri)
     */
    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    /**
     * Memperbarui data tarif dan layanan yang sudah ada
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'service_name'   => 'required|string|max:255',
            'category'       => 'required|in:Kiloan,Satuan,Express',
            'price_per_unit' => 'required|integer|min:0',
            'unit_type'      => 'required|in:Kg,Pcs',
            'description'    => 'nullable|string',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')->with('success', 'Paket layanan laundry berhasil diperbarui!');
    }

    /**
     * Menghapus paket layanan dari basis data
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index')->with('success', 'Paket layanan laundry berhasil dihapus!');
    }
}
