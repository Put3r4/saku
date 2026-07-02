@extends('layouts.admin')

@section('title', 'Input Matriks Nilai')

@section('header', 'Input Matriks Nilai (x_ij)')

@section('content')
<div class="max-w-full mx-auto">
    {{-- Pesan Sukses/Error --}}
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
            <strong>✓ Berhasil:</strong> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
            <strong>✗ Gagal:</strong> {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
            <strong>✗ Validasi Gagal:</strong>
            <ul class="list-disc list-inside mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Informasi Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-bold text-saku-dark mb-2">Panduan Pengisian Matrix</h3>
        <ul class="text-sm text-saku-muted space-y-1">
            <li>• <strong>Baris:</strong> Setiap menu yang tersedia</li>
            <li>• <strong>Kolom:</strong> Setiap kriteria (Kode + Tipe)</li>
            <li>• <strong>Nilai:</strong> Masukkan nilai numerik ≥ 0 untuk setiap sel</li>
            <li>• <strong>Warning:</strong> Sel dengan <span class="text-red-600 font-bold">border merah</span> menandakan data belum diisi atau bernilai 0</li>
        </ul>
    </div>

    {{-- Form Matrix --}}
    <form action="{{ route('admin.matrix.update') }}" method="POST" id="matrixForm">
        @csrf

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-sm">
                    <thead class="bg-saku-dark text-white">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold border border-gray-300 sticky left-0 bg-saku-dark z-10">
                                Menu
                            </th>
                            @foreach ($criteria as $criterion)
                                <th class="px-3 py-3 text-center font-bold border border-gray-300 min-w-[120px]">
                                    <div class="flex flex-col items-center space-y-1">
                                        <span class="text-base">{{ $criterion->kode }}</span>
                                        <span class="text-xs font-normal opacity-90">{{ $criterion->nama_kriteria }}</span>
                                        <span class="px-2 py-0.5 text-xs rounded-full {{ $criterion->tipe === 'benefit' ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300' }}">
                                            {{ ucfirst($criterion->tipe) }}
                                        </span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($menus as $menu)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 border border-gray-300 font-semibold text-saku-dark sticky left-0 bg-white z-10">
                                    <div class="flex flex-col">
                                        <span class="text-sm">{{ $menu->vendor_name }}</span>
                                        <span class="text-xs text-saku-muted">{{ $menu->menu_name }}</span>
                                    </div>
                                </td>
                                @foreach ($criteria as $criterion)
                                    @php
                                        $existingValue = $evaluations->get($menu->id)?->get($criterion->id)?->value ?? null;
                                        $isEmpty = is_null($existingValue) || $existingValue == 0;
                                    @endphp
                                    <td class="px-2 py-2 border border-gray-300 text-center">
                                        <input 
                                            type="number" 
                                            name="values[{{ $menu->id }}][{{ $criterion->id }}]" 
                                            value="{{ old("values.{$menu->id}.{$criterion->id}", $existingValue) }}"
                                            step="0.01"
                                            min="0"
                                            class="w-full px-2 py-1.5 text-center border rounded focus:ring-2 focus:ring-saku-accent focus:border-saku-accent {{ $isEmpty ? 'border-red-500 border-2 bg-red-50' : 'border-gray-300' }}"
                                            placeholder="0.00"
                                            required
                                        >
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($criteria) + 1 }}" class="px-4 py-8 text-center text-saku-muted">
                                    Tidak ada menu yang tersedia. Silakan tambahkan menu terlebih dahulu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tombol Simpan --}}
            @if($menus->count() > 0 && $criteria->count() > 0)
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-saku-accent hover:bg-saku-primary text-white font-bold rounded-lg transition shadow-md hover:shadow-lg"
                    >
                        💾 Simpan Semua Nilai Matrix
                    </button>
                    <span class="ml-4 text-sm text-saku-muted">
                        Total: {{ $menus->count() }} menu × {{ $criteria->count() }} kriteria = {{ $menus->count() * $criteria->count() }} nilai
                    </span>
                </div>
            @endif
        </div>
    </form>

    {{-- Warning untuk Data Tidak Lengkap --}}
    @if($menus->count() > 0 && $criteria->count() > 0)
        @php
            $totalCells = $menus->count() * $criteria->count();
            $filledCells = 0;
            foreach ($menus as $menu) {
                foreach ($criteria as $criterion) {
                    $value = $evaluations->get($menu->id)?->get($criterion->id)?->value ?? null;
                    if (!is_null($value) && $value > 0) {
                        $filledCells++;
                    }
                }
            }
            $emptyCells = $totalCells - $filledCells;
        @endphp

        @if($emptyCells > 0)
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg">
                <strong>⚠ Perhatian:</strong> Terdapat <strong>{{ $emptyCells }}</strong> sel yang belum diisi atau bernilai 0. 
                Data yang tidak lengkap akan mengacaukan perhitungan SAW.
            </div>
        @else
            <div class="mt-6 p-4 bg-green-50 border border-green-300 text-green-800 rounded-lg">
                <strong>✓ Data Lengkap:</strong> Semua nilai matrix telah diisi. Perhitungan SAW siap dilakukan.
            </div>
        @endif
    @endif
</div>

<script>
    // Auto-focus pada sel pertama yang kosong
    document.addEventListener('DOMContentLoaded', function() {
        const emptyInput = document.querySelector('input[type="number"].border-red-500');
        if (emptyInput) {
            emptyInput.focus();
        }
    });

    // Konfirmasi sebelum submit jika masih ada nilai kosong
    document.getElementById('matrixForm').addEventListener('submit', function(e) {
        const emptyInputs = document.querySelectorAll('input[type="number"].border-red-500');
        if (emptyInputs.length > 0) {
            const confirmed = confirm(`Masih ada ${emptyInputs.length} sel yang belum diisi atau bernilai 0. Apakah Anda yakin ingin menyimpan?`);
            if (!confirmed) {
                e.preventDefault();
            }
        }
    });
</script>
@endsection
