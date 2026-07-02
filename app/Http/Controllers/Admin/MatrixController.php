<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Criterion;
use App\Models\MenuEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatrixController extends Controller
{
    /**
     * Display the matrix input form.
     * Shows a grid of all available menus (rows) × all criteria (columns).
     */
    public function index()
    {
        // Ambil semua menu yang tersedia
        $menus = Menu::where('is_available', true)
            ->orderBy('vendor_name')
            ->orderBy('menu_name')
            ->get();

        // Ambil semua kriteria, urutkan berdasarkan kode
        $criteria = Criterion::orderBy('kode')->get();

        // Ambil semua nilai evaluasi yang sudah ada
        // Kita buat map untuk akses cepat: [menu_id][criterion_id] => value
        $evaluations = MenuEvaluation::all()
            ->groupBy('menu_id')
            ->map(function ($group) {
                return $group->keyBy('criterion_id');
            });

        return view('admin.matrix.index', compact('menus', 'criteria', 'evaluations'));
    }

    /**
     * Update or create matrix values.
     * Receives array of values[menu_id][criterion_id] and saves to menu_evaluations.
     */
    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'values' => 'required|array',
            'values.*' => 'required|array',
            'values.*.*' => 'required|numeric|min:0',
        ], [
            'values.required' => 'Data matrix tidak boleh kosong.',
            'values.*.*.required' => 'Semua nilai harus diisi.',
            'values.*.*.numeric' => 'Nilai harus berupa angka.',
            'values.*.*.min' => 'Nilai tidak boleh negatif.',
        ]);

        try {
            DB::beginTransaction();

            $values = $request->input('values');
            $updatedCount = 0;

            foreach ($values as $menuId => $criteriaValues) {
                foreach ($criteriaValues as $criterionId => $value) {
                    MenuEvaluation::updateOrCreate(
                        [
                            'menu_id' => $menuId,
                            'criterion_id' => $criterionId,
                        ],
                        [
                            'value' => $value,
                        ]
                    );
                    $updatedCount++;
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.matrix.index')
                ->with('success', "Berhasil menyimpan {$updatedCount} nilai matrix.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
}
