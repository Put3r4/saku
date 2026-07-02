@extends('layouts.admin')

@section('title', 'Dasbor Utama')
@section('header', 'Ringkasan Sistem')

@section('content')
    <div class="mb-8">
        <h3 class="text-gray-500 font-medium">Selamat datang kembali, {{ Str::before(Auth::user()->name, ' ') }}!</h3>
        <p class="text-sm text-saku-muted">Berikut adalah statistik terkini dari mesin rekomendasi SAKU Anda.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-saku-accent flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-saku-muted mb-1">TOTAL MENU MAKANAN</p>
                <p class="text-3xl font-extrabold text-saku-dark">{{ $stats['totalMenus'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-saku-light flex items-center justify-center text-saku-primary text-xl font-bold">M</div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-saku-dark flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-saku-muted mb-1">KRITERIA GIZI (SAW)</p>
                <p class="text-3xl font-extrabold text-saku-dark">{{ $stats['totalCriteria'] }}</p>
                <p class="text-xs text-saku-muted mt-1">Total Bobot: {{ number_format($stats['totalWeight'], 2) }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 text-xl font-bold">K</div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-green-500 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-saku-muted mb-1">MAHASISWA AKTIF</p>
                <p class="text-3xl font-extrabold text-saku-dark">{{ $stats['totalStudents'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-500 text-xl font-bold">U</div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-purple-500 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-saku-muted mb-1">REKOMENDASI DIBERIKAN</p>
                <p class="text-3xl font-extrabold text-saku-dark">{{ $stats['totalRecommendations'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center text-purple-500 text-xl font-bold">R</div>
        </div>
    </div>

    @if($stats['totalMenus'] > 0 && $stats['totalCriteria'] > 0)
        <!-- Recent Activities Section -->
        @if($recentActivities->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h4 class="text-lg font-bold text-saku-dark mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-saku-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Aktivitas Terakhir
            </h4>
            <div class="space-y-3">
                @foreach($recentActivities as $activity)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-saku-dark">
                            {{ $activity['user_name'] }} memilih 
                            <span class="font-bold text-saku-accent">{{ $activity['menu_name'] }}</span>
                        </p>
                        <p class="text-xs text-saku-muted mt-1">
                            Budget: Rp {{ number_format($activity['budget'], 0, ',', '.') }} • 
                            Skor: {{ number_format($activity['saw_score'], 4) }} • 
                            {{ $activity['created_at']->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- System Status -->
        <div class="bg-green-50 rounded-2xl border border-green-200 p-6 text-center">
            <div class="w-16 h-16 mx-auto bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h4 class="text-lg font-bold text-green-900 mb-2">Sistem Siap Digunakan!</h4>
            <p class="text-sm text-green-700">
                Mesin rekomendasi SAW aktif dengan {{ $stats['totalCriteria'] }} kriteria dan {{ $stats['totalMenus'] }} menu.
            </p>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center py-16">
            <div class="w-16 h-16 mx-auto bg-saku-light text-saku-primary rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h4 class="text-lg font-bold text-saku-dark mb-2">Belum Ada Data</h4>
            <p class="text-saku-muted mb-6">Silakan mulai dengan menambahkan Kriteria Penilaian dan Data Menu Makanan melalui menu di samping kiri.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('admin.criteria.index') }}" class="inline-flex items-center px-5 py-2.5 bg-saku-accent text-white text-sm font-semibold rounded-lg hover:bg-saku-primary transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Kriteria
                </a>
                <a href="{{ route('admin.menu.index') }}" class="inline-flex items-center px-5 py-2.5 bg-saku-dark text-white text-sm font-semibold rounded-lg hover:bg-opacity-90 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Menu
                </a>
            </div>
        </div>
    @endif
@endsection