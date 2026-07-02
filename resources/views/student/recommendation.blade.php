@extends('layouts.student')

@section('title', 'Rekomendasi Menu')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('student.dashboard') }}" class="inline-flex items-center text-saku-muted hover:text-saku-dark font-medium mb-4 transition">
            <span class="mr-2">←</span> Kembali ke Dashboard
        </a>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h1 class="text-2xl font-bold text-saku-dark mb-2">
                🎯 Rekomendasi Menu untuk Budget Rp {{ number_format($budget, 0, ',', '.') }}
            </h1>
            <p class="text-saku-muted">
                Berikut adalah menu terbaik yang kami rekomendasikan berdasarkan budget Anda.
            </p>
        </div>
    </div>

    @if($recommendations->isEmpty())
        <!-- No Results -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
            <div class="text-6xl mb-4">😔</div>
            <h2 class="text-xl font-bold text-yellow-800 mb-2">
                Tidak ada menu yang sesuai dengan budget Anda
            </h2>
            <p class="text-yellow-700 mb-6">
                Coba naikkan budget Anda atau hubungi admin untuk informasi menu dengan harga lebih rendah.
            </p>
            <a href="{{ route('student.dashboard') }}" class="inline-block bg-saku-accent hover:bg-saku-primary text-white font-bold py-3 px-6 rounded-lg transition">
                Coba Budget Lain
            </a>
        </div>
    @else
        <!-- Recommendations List -->
        <div class="space-y-4">
            @foreach($recommendations as $index => $recommendation)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition duration-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Rank Badge -->
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700' }} font-bold text-lg">
                                        {{ $index + 1 }}
                                    </span>
                                    
                                    @if($index === 0)
                                        <span class="inline-block bg-gradient-to-r from-yellow-400 to-yellow-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                                            ⭐ REKOMENDASI TERBAIK
                                        </span>
                                    @endif
                                </div>

                                <!-- Menu Info -->
                                <h3 class="text-xl font-bold text-saku-dark mb-2">
                                    {{ $recommendation['menu_name'] }}
                                </h3>
                                
                                <div class="flex items-center gap-4 text-sm text-saku-muted mb-4">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        {{ $recommendation['vendor'] }}
                                    </span>
                                    
                                    <span class="flex items-center gap-1 font-semibold text-green-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Rp {{ number_format($recommendation['price'], 0, ',', '.') }}
                                    </span>
                                </div>

                                <!-- SAW Score -->
                                <div class="inline-block bg-saku-light px-4 py-2 rounded-lg">
                                    <span class="text-xs font-medium text-saku-muted">Skor SAW:</span>
                                    <span class="text-lg font-bold text-saku-dark ml-2">{{ number_format($recommendation['saw_score'], 4) }}</span>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="ml-6">
                                <form action="{{ route('student.select-menu') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="menu_id" value="{{ $recommendation['menu_id'] }}">
                                    <input type="hidden" name="budget" value="{{ $budget }}">
                                    <button 
                                        type="submit"
                                        class="bg-saku-accent hover:bg-saku-primary text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-sm hover:shadow-md whitespace-nowrap"
                                    >
                                        ✓ Pilih Menu Ini
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Additional Info (optional, could show criteria scores here later) -->
                        @if($index === 0)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <p class="text-xs text-saku-muted">
                                    💡 Menu ini memiliki skor tertinggi berdasarkan kriteria: Harga, Kalori, Rating, Protein, dan Jarak.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Bottom Actions -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
            <p class="text-saku-muted mb-4">
                Tidak menemukan yang cocok? Coba ubah budget Anda atau lihat riwayat pilihan sebelumnya.
            </p>
            <div class="flex justify-center gap-3">
                <a href="{{ route('student.dashboard') }}" class="inline-block bg-gray-100 hover:bg-gray-200 text-saku-dark font-semibold py-3 px-6 rounded-lg transition">
                    Ubah Budget
                </a>
                <a href="{{ route('student.history.index') }}" class="inline-block bg-saku-light hover:bg-saku-accent hover:text-white text-saku-dark font-semibold py-3 px-6 rounded-lg transition">
                    📊 Lihat Riwayat
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
