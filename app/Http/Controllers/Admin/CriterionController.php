<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Criterion;
use Illuminate\Http\Request;

class CriterionController extends Controller
{
    public function index()
    {
        // Mengambil semua data kriteria dan mengurutkannya berdasarkan kode (C1, C2, dst)
        $criteria = Criterion::orderBy('kode', 'asc')->get();
        
        return view('admin.criteria.index', compact('criteria'));
    }

    public function create()
    {
        // Menampilkan halaman form tambah kriteria
        return view('admin.criteria.create');
    }

    public function store(Request $request)
    {
     
        $validated = $request->validate([
            'kode' => ['required', 'unique:criteria,kode', 'max:10'],
            'nama_kriteria' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'in:benefit,cost'],
            'bobot' => ['required', 'numeric', 'min:0.01', 'max:1.00'],
        ], [
            'kode.unique' => 'Kode kriteria ini sudah digunakan, silakan gunakan kode lain (misal: C2).',
            'bobot.max' => 'Total bobot tidak boleh lebih dari 1.00 (100%).'
        ]);
        Criterion::create($validated);
        return redirect()->route('admin.criteria.index')->with('success', 'Kriteria baru berhasil ditambahkan ke sistem!');
    }
}