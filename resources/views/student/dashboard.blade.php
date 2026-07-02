@extends('layouts.student')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Hero Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-8">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-saku-dark mb-2">
                Selamat Datang, {{ Auth::user()->name }}! 👋
            </h1>
            <p class="text-saku-muted">
                Masukkan budget harian Anda, dan kami akan merekomendasikan menu terbaik untukmu.
            </p>
        </div>

        <!-- Budget Input Form -->
        <form action="{{ route('student.recommend') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="budget" class="block text-sm font-semibold text-saku-dark mb-2">
                    Budget Harian (Rp)
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-saku-muted font-medium">
                        Rp
                    </span>
                    <input 
                        type="text" 
                        name="budget" 
                        id="budget" 
                        value="{{ old('budget') }}"
                        placeholder="Contoh: 25000"
                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-saku-accent focus:border-transparent text-lg @error('budget') border-red-500 @enderror"
                        oninput="formatCurrency(this)"
                    >
                </div>
                @error('budget')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-saku-muted">
                    💡 Budget minimal Rp 1.000 dan maksimal Rp 1.000.000
                </p>
            </div>

            <button 
                type="submit" 
                class="w-full bg-saku-accent hover:bg-saku-primary text-white font-bold py-4 px-6 rounded-lg transition duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
            >
                🔍 Cari Rekomendasi Menu
            </button>
        </form>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <div class="text-3xl mb-2">🍜</div>
            <h3 class="font-semibold text-saku-dark mb-1">Menu Beragam</h3>
            <p class="text-xs text-saku-muted">Berbagai pilihan dari vendor terpercaya</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <div class="text-3xl mb-2">📊</div>
            <h3 class="font-semibold text-saku-dark mb-1">Metode SAW</h3>
            <p class="text-xs text-saku-muted">Rekomendasi berbasis algoritma ilmiah</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <div class="text-3xl mb-2">💰</div>
            <h3 class="font-semibold text-saku-dark mb-1">Sesuai Budget</h3>
            <p class="text-xs text-saku-muted">Hanya menu yang sesuai anggaranmu</p>
        </div>
    </div>

    <!-- Quick Link to History -->
    <div class="bg-gradient-to-r from-saku-light to-saku-accent/20 rounded-lg p-6 text-center">
        <p class="text-saku-dark font-medium mb-3">
            Sudah pernah mencari rekomendasi sebelumnya?
        </p>
        <a href="{{ route('student.history.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-saku-dark font-semibold py-2 px-6 rounded-lg transition shadow-sm">
            <span>📊</span>
            <span>Lihat Riwayat Pilihan</span>
        </a>
    </div>
</div>

<script>
function formatCurrency(input) {
    // Remove all non-numeric characters
    let value = input.value.replace(/\D/g, '');
    
    // Convert to number and format with thousands separator
    if (value) {
        // Store raw value for form submission
        input.value = value;
    }
}
</script>
@endsection
