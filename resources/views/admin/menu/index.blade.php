@extends('layouts.admin')

@section('title', 'Kelola Menu')
@section('header', 'Data Menu & Harga')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h3 class="text-saku-dark font-bold text-lg">Daftar Menu Makanan</h3>
            <p class="text-sm text-saku-muted">Kelola data menu, vendor, harga, dan ketersediaan untuk sistem rekomendasi.</p>
        </div>
        <a href="{{ route('admin.menu.create') }}" class="px-5 py-2.5 bg-saku-accent text-white rounded-xl font-bold text-sm shadow-md hover:bg-saku-primary hover:shadow-lg transition duration-300 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Menu</span>
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
                    <th class="py-4 px-6 font-bold text-sm text-saku-dark">Nama Vendor</th>
                    <th class="py-4 px-6 font-bold text-sm text-saku-dark">Nama Menu</th>
                    <th class="py-4 px-6 font-bold text-sm text-saku-dark">Harga</th>
                    <th class="py-4 px-6 font-bold text-sm text-saku-dark">Status</th>
                    <th class="py-4 px-6 font-bold text-sm text-saku-dark text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $menu)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                        <td class="py-4 px-6 text-sm font-semibold text-saku-primary">{{ $menu->vendor_name }}</td>
                        <td class="py-4 px-6 text-sm text-saku-dark">{{ $menu->menu_name }}</td>
                        <td class="py-4 px-6 text-sm text-saku-dark font-medium">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                        <td class="py-4 px-6 text-sm">
                            @if($menu->is_available)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Tersedia</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Tidak Tersedia</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-sm text-right space-x-2">
                            <a href="{{ route('admin.menu.edit', $menu) }}" class="text-saku-muted hover:text-saku-accent transition font-semibold">Edit</a>
                            <form action="{{ route('admin.menu.destroy', $menu) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu {{ $menu->menu_name }} dari {{ $menu->vendor_name }}?\n\n⚠️ PERHATIAN: Jika menu ini memiliki data evaluasi (nilai kriteria) terkait, semua data evaluasi tersebut juga akan ikut terhapus secara permanen (cascade delete).')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-saku-muted hover:text-red-500 transition font-semibold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-saku-muted">
                            Belum ada data menu yang ditambahkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($menus->count() > 0)
        <div class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 text-sm rounded-r-xl">
            <strong>Total Menu:</strong> {{ $menus->count() }} menu terdaftar dalam sistem.
        </div>
    @endif
@endsection
