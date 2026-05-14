@extends('layouts.admin')

@section('title', 'Dasbor Utama')
@section('header', 'Ringkasan Sistem')

@section('content')
    <div class="mb-8">
        <h3 class="text-gray-500 font-medium">Selamat datang kembali, {{ Str::before(Auth::user()->name, ' ') }}!</h3>
        <p class="text-sm text-saku-muted">Berikut adalah statistik terkini dari mesin rekomendasi SAKU Anda.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-saku-accent flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-saku-muted mb-1">TOTAL MENU MAKANAN</p>
                <p class="text-3xl font-extrabold text-saku-dark">0</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-saku-light flex items-center justify-center text-saku-primary text-xl font-bold">M</div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-saku-dark flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-saku-muted mb-1">KRITERIA GIZI (SAW)</p>
                <p class="text-3xl font-extrabold text-saku-dark">0</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 text-xl font-bold">K</div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-green-500 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-saku-muted mb-1">MAHASISWA AKTIF</p>
                <p class="text-3xl font-extrabold text-saku-dark">1</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-500 text-xl font-bold">U</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center py-16">
        <div class="w-16 h-16 mx-auto bg-saku-light text-saku-primary rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h4 class="text-lg font-bold text-saku-dark mb-2">Belum Ada Data</h4>
        <p class="text-saku-muted">Silakan mulai dengan menambahkan Kriteria Penilaian dan Data Menu Makanan melalui menu di samping kiri.</p>
    </div>
@endsection