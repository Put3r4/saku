<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Criterion;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CriterionController extends Controller
{
    public function index()
    {
        // Mengambil semua data kriteria dan mengurutkannya berdasarkan kode (C1, C2, dst)
        $criteria = Criterion::orderBy('kode', 'asc')->get();
        
        // Hitung total bobot saat ini
        $totalBobot = $criteria->sum('bobot');
        
        return view('admin.criteria.index', compact('criteria', 'totalBobot'));
    }

    public function create()
    {
        // Hitung total bobot yang sudah ada
        $totalBobot = Criterion::sum('bobot');
        $sisaBobot = 1.00 - $totalBobot;
        
        return view('admin.criteria.create', compact('sisaBobot'));
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
            'bobot.max' => 'Bobot tidak boleh lebih dari 1.00 (100%).'
        ]);
        
        // Validasi: cek apakah total bobot setelah ditambah melebihi 1.00
        $currentTotalBobot = Criterion::sum('bobot');
        $newTotalBobot = $currentTotalBobot + $validated['bobot'];
        
        if ($newTotalBobot > 1.0001) { // toleransi floating point
            throw ValidationException::withMessages([
                'bobot' => sprintf(
                    'Total bobot akan melebihi 1.00! Total saat ini: %.4f, bobot yang diinput: %.4f, total akan menjadi: %.4f. Maksimal bobot yang bisa diinput: %.4f',
                    $currentTotalBobot,
                    $validated['bobot'],
                    $newTotalBobot,
                    1.00 - $currentTotalBobot
                )
            ]);
        }
        
        Criterion::create($validated);
        
        return redirect()->route('admin.criteria.index')->with('success', 'Kriteria baru berhasil ditambahkan ke sistem!');
    }

    public function edit(Criterion $criterion)
    {
        // Hitung total bobot saat ini (exclude kriteria yang sedang diedit)
        $totalBobotLainnya = Criterion::where('id', '!=', $criterion->id)->sum('bobot');
        $sisaBobot = 1.00 - $totalBobotLainnya;
        
        return view('admin.criteria.edit', compact('criterion', 'sisaBobot'));
    }

    public function update(Request $request, Criterion $criterion)
    {
        $validated = $request->validate([
            'kode' => ['required', 'max:10', 'unique:criteria,kode,' . $criterion->id],
            'nama_kriteria' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'in:benefit,cost'],
            'bobot' => ['required', 'numeric', 'min:0.01', 'max:1.00'],
        ], [
            'kode.unique' => 'Kode kriteria ini sudah digunakan oleh kriteria lain.',
            'bobot.max' => 'Bobot tidak boleh lebih dari 1.00 (100%).'
        ]);
        
        // Validasi: cek apakah total bobot setelah diupdate melebihi 1.00
        // Total = total bobot kriteria lain + bobot baru yang diinput
        $totalBobotLainnya = Criterion::where('id', '!=', $criterion->id)->sum('bobot');
        $newTotalBobot = $totalBobotLainnya + $validated['bobot'];
        
        if ($newTotalBobot > 1.0001) { // toleransi floating point
            throw ValidationException::withMessages([
                'bobot' => sprintf(
                    'Total bobot akan melebihi 1.00! Total bobot kriteria lain: %.4f, bobot yang diinput: %.4f, total akan menjadi: %.4f. Maksimal bobot yang bisa diinput: %.4f',
                    $totalBobotLainnya,
                    $validated['bobot'],
                    $newTotalBobot,
                    1.00 - $totalBobotLainnya
                )
            ]);
        }
        
        $criterion->update($validated);
        
        return redirect()->route('admin.criteria.index')->with('success', 'Kriteria berhasil diperbarui!');
    }

    public function destroy(Criterion $criterion)
    {
        $criterion->delete();
        
        return redirect()->route('admin.criteria.index')->with('success', 'Kriteria berhasil dihapus dari sistem!');
    }
}