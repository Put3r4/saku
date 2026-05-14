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
                            <button class="text-saku-muted hover:text-saku-accent transition">Edit</button>
                            <button class="text-saku-muted hover:text-red-500 transition">Hapus</button>
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