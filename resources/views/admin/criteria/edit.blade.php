@extends('layouts.admin')

@section('title', 'Edit Kriteria')
@section('header', 'Edit Kriteria')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.criteria.index') }}" class="text-sm font-semibold text-saku-muted hover:text-saku-accent transition flex items-center space-x-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
        <div class="p-8">
            <h3 class="text-xl font-bold text-saku-dark mb-2">Formulir Edit Kriteria</h3>
            <div class="mb-6 p-3 bg-blue-50 border-l-4 border-blue-500 text-blue-700 text-sm rounded-r-xl">
                <strong>Info:</strong> Sisa kuota bobot yang dapat digunakan: <strong>{{ number_format($sisaBobot, 4) }}</strong> (dari kriteria lain)
            </div>

            <form action="{{ route('admin.criteria.update', $criterion) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="kode" class="block text-sm font-bold text-saku-dark mb-2">Kode Kriteria</label>
                        <input type="text" id="kode" name="kode" value="{{ old('kode', $criterion->kode) }}" placeholder="Contoh: C1" required
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('kode') ? 'border-red-500' : 'border-gray-300' }} focus:border-saku-accent focus:ring-2 focus:ring-saku-accent/20 outline-none transition text-saku-dark bg-gray-50 focus:bg-white">
                        @error('kode') <span class="text-red-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="nama_kriteria" class="block text-sm font-bold text-saku-dark mb-2">Nama Kriteria</label>
                        <select id="nama_kriteria" name="nama_kriteria" required
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('nama_kriteria') ? 'border-red-500' : 'border-gray-300' }} focus:border-saku-accent focus:ring-2 focus:ring-saku-accent/20 outline-none transition text-saku-dark bg-gray-50 focus:bg-white appearance-none">
                        
                            <option value="" disabled>Pilih Kriteria Baku...</option>

                            <optgroup label="--- Kategori Nutrisi & Gizi ---" class="font-bold text-saku-primary">
                                <option value="Kandungan Protein" {{ old('nama_kriteria', $criterion->nama_kriteria) == 'Kandungan Protein' ? 'selected' : '' }}>Kandungan Protein</option>
                                <option value="Kandungan Karbohidrat / Kalori" {{ old('nama_kriteria', $criterion->nama_kriteria) == 'Kandungan Karbohidrat / Kalori' ? 'selected' : '' }}>Kandungan Karbohidrat / Kalori</option>
                                <option value="Kandungan Serat / Sayur" {{ old('nama_kriteria', $criterion->nama_kriteria) == 'Kandungan Serat / Sayur' ? 'selected' : '' }}>Kandungan Serat / Sayur</option>
                            </optgroup>

                            <optgroup label="--- Kategori Kepuasan & Kualitas ---" class="font-bold text-saku-primary mt-2">
                                <option value="Ukuran Porsi Mengenyangkan" {{ old('nama_kriteria', $criterion->nama_kriteria) == 'Ukuran Porsi Mengenyangkan' ? 'selected' : '' }}>Ukuran Porsi Mengenyangkan</option>
                                <option value="Rating / Ulasan Rasa" {{ old('nama_kriteria', $criterion->nama_kriteria) == 'Rating / Ulasan Rasa' ? 'selected' : '' }}>Rating / Ulasan Rasa</option>
                                <option value="Tingkat Kebersihan Tempat" {{ old('nama_kriteria', $criterion->nama_kriteria) == 'Tingkat Kebersihan Tempat' ? 'selected' : '' }}>Tingkat Kebersihan Tempat</option>
                            </optgroup>

                            <optgroup label="--- Kategori Aksesibilitas ---" class="font-bold text-saku-primary mt-2">
                                <option value="Jarak ke Warung Makan" {{ old('nama_kriteria', $criterion->nama_kriteria) == 'Jarak ke Warung Makan' ? 'selected' : '' }}>Jarak ke Warung Makan</option>
                                <option value="Waktu Penyajian" {{ old('nama_kriteria', $criterion->nama_kriteria) == 'Waktu Penyajian' ? 'selected' : '' }}>Waktu Penyajian</option>
                            </optgroup>

                        </select>
                        @error('nama_kriteria') <span class="text-red-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="tipe" class="block text-sm font-bold text-saku-dark mb-2">Tipe Atribut</label>
                        <select id="tipe" name="tipe" required
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('tipe') ? 'border-red-500' : 'border-gray-300' }} focus:border-saku-accent focus:ring-2 focus:ring-saku-accent/20 outline-none transition text-saku-dark bg-gray-50 focus:bg-white appearance-none">
                            <option value="" disabled>Pilih tipe...</option>
                            <option value="benefit" {{ old('tipe', $criterion->tipe) == 'benefit' ? 'selected' : '' }}>Benefit (Semakin besar semakin baik)</option>
                            <option value="cost" {{ old('tipe', $criterion->tipe) == 'cost' ? 'selected' : '' }}>Cost (Semakin kecil semakin baik)</option>
                        </select>
                        @error('tipe') <span class="text-red-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="bobot" class="block text-sm font-bold text-saku-dark mb-2">Bobot Kepentingan (0.01 - 1.00)</label>
                        <input type="number" step="0.01" min="0" max="1" id="bobot" name="bobot" value="{{ old('bobot', $criterion->bobot) }}" placeholder="Contoh: 0.30" required
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('bobot') ? 'border-red-500' : 'border-gray-300' }} focus:border-saku-accent focus:ring-2 focus:ring-saku-accent/20 outline-none transition text-saku-dark bg-gray-50 focus:bg-white">
                        @error('bobot') <span class="text-red-500 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <button type="submit" class="px-8 py-3.5 bg-saku-dark text-saku-light font-bold rounded-xl shadow-lg hover:bg-saku-primary transform hover:-translate-y-0.5 transition duration-300">
                        Perbarui Kriteria
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
