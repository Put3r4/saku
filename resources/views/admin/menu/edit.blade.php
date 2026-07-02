@extends('layouts.admin')

@section('title', 'Edit Menu')
@section('header', 'Edit Menu')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.menu.index') }}" class="text-sm font-semibold text-saku-muted hover:text-saku-accent transition flex items-center space-x-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
        <div class="p-8">
            <h3 class="text-xl font-bold text-saku-dark mb-2">Formulir Edit Menu</h3>
            <p class="text-sm text-saku-muted mb-6">Perbarui informasi menu <strong>{{ $menu->menu_name }}</strong> dari <strong>{{ $menu->vendor_name }}</strong>.</p>

            <form action="{{ route('admin.menu.update', $menu) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="vendor_name" class="block text-sm font-bold text-saku-dark mb-2">Nama Vendor / Warung</label>
                        <input type="text" id="vendor_name" name="vendor_name" value="{{ old('vendor_name', $menu->vendor_name) }}" placeholder="Contoh: Warung Bu Tini" required
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('vendor_name') ? 'border-red-500' : 'border-gray-300' }} focus:border-saku-accent focus:ring-2 focus:ring-saku-accent/20 outline-none transition text-saku-dark bg-gray-50 focus:bg-white">
                        @error('vendor_name') <span class="text-red-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="menu_name" class="block text-sm font-bold text-saku-dark mb-2">Nama Menu</label>
                        <input type="text" id="menu_name" name="menu_name" value="{{ old('menu_name', $menu->menu_name) }}" placeholder="Contoh: Nasi Goreng Spesial" required
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('menu_name') ? 'border-red-500' : 'border-gray-300' }} focus:border-saku-accent focus:ring-2 focus:ring-saku-accent/20 outline-none transition text-saku-dark bg-gray-50 focus:bg-white">
                        @error('menu_name') <span class="text-red-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="price" class="block text-sm font-bold text-saku-dark mb-2">Harga (Rp)</label>
                        <input type="number" step="0.01" min="0" id="price" name="price" value="{{ old('price', $menu->price) }}" placeholder="Contoh: 15000" required
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('price') ? 'border-red-500' : 'border-gray-300' }} focus:border-saku-accent focus:ring-2 focus:ring-saku-accent/20 outline-none transition text-saku-dark bg-gray-50 focus:bg-white">
                        @error('price') <span class="text-red-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="image_url" class="block text-sm font-bold text-saku-dark mb-2">URL Gambar (Opsional)</label>
                        <input type="url" id="image_url" name="image_url" value="{{ old('image_url', $menu->image_url) }}" placeholder="https://example.com/image.jpg"
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('image_url') ? 'border-red-500' : 'border-gray-300' }} focus:border-saku-accent focus:ring-2 focus:ring-saku-accent/20 outline-none transition text-saku-dark bg-gray-50 focus:bg-white">
                        @error('image_url') <span class="text-red-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-bold text-saku-dark mb-2">Deskripsi (Opsional)</label>
                    <textarea id="description" name="description" rows="3" placeholder="Deskripsi singkat tentang menu ini..."
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('description') ? 'border-red-500' : 'border-gray-300' }} focus:border-saku-accent focus:ring-2 focus:ring-saku-accent/20 outline-none transition text-saku-dark bg-gray-50 focus:bg-white">{{ old('description', $menu->description) }}</textarea>
                    @error('description') <span class="text-red-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="mb-8">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" id="is_available" name="is_available" value="1" {{ old('is_available', $menu->is_available) ? 'checked' : '' }}
                            class="w-5 h-5 text-saku-accent border-gray-300 rounded focus:ring-saku-accent focus:ring-2">
                        <span class="text-sm font-bold text-saku-dark">Menu Tersedia (Aktif)</span>
                    </label>
                    <p class="text-xs text-saku-muted mt-1 ml-8">Centang jika menu ini tersedia untuk dipilih mahasiswa.</p>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <button type="submit" class="px-8 py-3.5 bg-saku-dark text-saku-light font-bold rounded-xl shadow-lg hover:bg-saku-primary transform hover:-translate-y-0.5 transition duration-300">
                        Perbarui Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
