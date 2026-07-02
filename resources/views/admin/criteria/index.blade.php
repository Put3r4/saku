@extends('layouts.admin')

@section('title', 'Kelola Kriteria')
@section('header', 'Manajemen Kriteria & Bobot (SAW)')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h3 class="text-saku-dark font-bold text-lg">Daftar Kriteria Penilaian</h3>
            <p class="text-sm text-saku-muted">Atur variabel gizi dan preferensi yang akan dihitung oleh sistem.</p>
        </div>
        <a href="{{ route('admin.criteria.create') }}" class="px-5 py-2.5 bg-saku-accent text-white rounded-xl font-bold text-sm shadow-md hover:bg-saku-primary hover:shadow-lg transition duration-300 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Kriteria</span>
        </a>
    </div>

    {{-- Indikator Total Bobot --}}
    <div class="mb-6 p-4 rounded-xl shadow-sm border @if(abs($totalBobot - 1.00) < 0.001) bg-green-50 border-green-300 @elseif($totalBobot > 1.00) bg-red-50 border-red-300 @else bg-yellow-50 border-yellow-300 @endif">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="font-bold text-sm @if(abs($totalBobot - 1.00) < 0.001) text-green-700 @elseif($totalBobot > 1.00) text-red-700 @else text-yellow-700 @endif">
                    Total Bobot Kriteria Saat Ini: {{ number_format($totalBobot, 4) }} / 1.00
                </h4>
                <p class="text-xs mt-1 @if(abs($totalBobot - 1.00) < 0.001) text-green-600 @elseif($totalBobot > 1.00) text-red-600 @else text-yellow-600 @endif">
                    @if(abs($totalBobot - 1.00) < 0.001)
                        ✓ Total bobot sudah sempurna! Sistem SAW siap digunakan.
                    @elseif($totalBobot > 1.00)
                        ✗ Total bobot melebihi 1.00! Harap kurangi bobot beberapa kriteria.
                    @else
                        ⚠ Sisa kuota bobot: {{ number_format(1.00 - $totalBobot, 4) }}. Tambahkan kriteria untuk melengkapi total 1.00.
                    @endif
                </p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold @if(abs($totalBobot - 1.00) < 0.001) text-green-600 @elseif($totalBobot > 1.00) text-red-600 @else text-yellow-600 @endif">
                    {{ number_format($totalBobot * 100, 2) }}%
                </div>
            </div>
        </div>
    </div>
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-semibold rounded-r-xl shadow-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.style.display='none'" class="text-green-700 hover:text-green-900">&times;</button>
        </div>
    @endif
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-saku-light/30 border-b border-gray-100">
                    <th class="py-4 px-6 font-bold text-sm text-saku-dark">Kode</th>
                    <th class="py-4 px-6 font-bold text-sm text-saku-dark">Nama Kriteria</th>
                    <th class="py-4 px-6 font-bold text-sm text-saku-dark">Tipe (Atribut)</th>
                    <th class="py-4 px-6 font-bold text-sm text-saku-dark">Bobot Kepentingan</th>
                    <th class="py-4 px-6 font-bold text-sm text-saku-dark text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($criteria as $item)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                        <td class="py-4 px-6 text-sm font-semibold text-saku-primary">{{ $item->kode }}</td>
                        <td class="py-4 px-6 text-sm text-saku-dark">{{ $item->nama_kriteria }}</td>
                        <td class="py-4 px-6 text-sm">
                            @if($item->tipe === 'benefit')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Benefit</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Cost</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-sm text-saku-dark font-medium">{{ $item->bobot }}</td>
                        <td class="py-4 px-6 text-sm text-right space-x-2">
                            <a href="{{ route('admin.criteria.edit', $item) }}" class="text-saku-muted hover:text-saku-accent transition font-semibold">Edit</a>
                            <form action="{{ route('admin.criteria.destroy', $item) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kriteria {{ $item->kode }} ({{ $item->nama_kriteria }})? Data evaluasi terkait kriteria ini juga akan terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-saku-muted hover:text-red-500 transition font-semibold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-saku-muted">
                            Belum ada data kriteria yang ditambahkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection