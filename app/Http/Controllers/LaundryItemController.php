<?php

namespace App\Http\Controllers;

use App\Models\LaundryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaundryItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $laundry_items = LaundryItem::latest()->paginate(10);

        return view('admin.laundry_items.index', compact('laundry_items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.laundry_items.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Cuci,Setrika,Paket',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('laundry_items', 'public');
        }

        LaundryItem::create([
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return redirect()->route('laundry_items.index')->with('success', 'Data layanan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(LaundryItem $laundryItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LaundryItem $laundryItem)
    {
        return view('admin.laundry_items.edit', compact('laundryItem'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LaundryItem $laundryItem)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Cuci,Setrika,Paket',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = $laundryItem->image;
        if ($request->hasFile('image')) {
            if ($laundryItem->image && Storage::disk('public')->exists($laundryItem->image)) {
                Storage::disk('public')->delete($laundryItem->image);
            }
            $imagePath = $request->file('image')->store('laundry_items', 'public');
        }

        $laundryItem->update([
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return redirect()->route('laundry_items.index')->with('success', 'Data layanan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LaundryItem $laundryItem)
    {
        if ($laundryItem->image && Storage::disk('public')->exists($laundryItem->image)) {
            Storage::disk('public')->delete($laundryItem->image);
        }
        $laundryItem->delete();

        return redirect()->route('laundry_items.index')->with('success', 'Data layanan berhasil dihapus!');
    }
}
