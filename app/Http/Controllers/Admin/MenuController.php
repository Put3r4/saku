<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        // Mengambil semua data menu dan mengurutkannya berdasarkan vendor_name, menu_name
        $menus = Menu::orderBy('vendor_name', 'asc')
                     ->orderBy('menu_name', 'asc')
                     ->get();
        
        return view('admin.menu.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menu.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_name' => ['required', 'string', 'max:255'],
            'menu_name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'is_available' => ['nullable', 'boolean'],
        ], [
            'vendor_name.required' => 'Nama vendor wajib diisi.',
            'menu_name.required' => 'Nama menu wajib diisi.',
            'price.required' => 'Harga menu wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh kurang dari 0.',
            'image_url.url' => 'URL gambar tidak valid.',
        ]);
        
        $validated['is_available'] = $request->boolean('is_available');
        
        Menu::create($validated);
        
        return redirect()->route('admin.menu.index')->with('success', 'Menu baru berhasil ditambahkan ke sistem!');
    }

    public function edit(Menu $menu)
    {
        return view('admin.menu.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'vendor_name' => ['required', 'string', 'max:255'],
            'menu_name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'is_available' => ['nullable', 'boolean'],
        ], [
            'vendor_name.required' => 'Nama vendor wajib diisi.',
            'menu_name.required' => 'Nama menu wajib diisi.',
            'price.required' => 'Harga menu wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh kurang dari 0.',
            'image_url.url' => 'URL gambar tidak valid.',
        ]);
        
        $validated['is_available'] = $request->boolean('is_available');
        
        $menu->update($validated);
        
        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy(Menu $menu)
    {
        // Cek apakah menu memiliki evaluasi terkait
        $hasEvaluations = $menu->menuEvaluations()->exists();
        
        $menu->delete();
        
        $message = 'Menu berhasil dihapus dari sistem!';
        if ($hasEvaluations) {
            $message .= ' Data evaluasi terkait menu ini juga telah dihapus.';
        }
        
        return redirect()->route('admin.menu.index')->with('success', $message);
    }
}
