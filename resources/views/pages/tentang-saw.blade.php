<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang SAW - SAKU</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        saku: {
                            dark: '#2A324A',
                            muted: '#7A82A6',
                            light: '#E1DBCB',
                            accent: '#D68438',
                            primary: '#8D4F37',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Figtree', sans-serif; }
    </style>
</head>
<body class="bg-saku-light text-saku-dark antialiased selection:bg-saku-accent selection:text-white">

    {{-- Navbar --}}
    <nav class="container mx-auto px-6 py-5 flex justify-between items-center">
        <a href="{{ route('home') }}" class="text-3xl font-extrabold tracking-wider text-saku-primary">
            SAKU<span class="text-saku-accent">.</span>
        </a>
        <div class="hidden md:flex space-x-10 text-base font-semibold">
            <a href="{{ route('home') }}" class="text-saku-muted hover:text-saku-dark transition duration-200">Beranda</a>
            <a href="{{ route('tentang-saw') }}" class="text-saku-dark border-b-2 border-saku-accent pb-1">Tentang SAW</a>
            <a href="{{ route('daftar-menu') }}" class="text-saku-muted hover:text-saku-dark transition duration-200">Daftar Menu</a>
        </div>
        <div class="flex items-center space-x-4">
            @guest
                <a href="{{ route('login') }}" class="px-6 py-2.5 bg-transparent border-2 border-saku-dark text-saku-dark rounded-full font-bold hover:bg-saku-dark hover:text-saku-light transition duration-300">
                    Masuk
                </a>
            @else
                <div class="flex items-center space-x-3">
                    <span class="text-sm font-bold text-saku-primary">
                        Halo, {{ Str::before(Auth::user()->name, ' ') }}!
                    </span>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-saku-dark text-white rounded-full font-bold text-sm hover:bg-saku-primary transition duration-300">
                            Dashboard Admin
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-5 py-2 bg-saku-accent text-white rounded-full font-bold text-sm hover:bg-saku-primary transition duration-300">
                            Keluar
                        </button>
                    </form>
                </div>
            @endguest
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="container mx-auto px-6 py-12">
        
        {{-- Hero Section --}}
        <div class="max-w-4xl mx-auto text-center mb-16">
            <div class="inline-block px-4 py-1.5 bg-saku-dark text-saku-light text-xs font-bold rounded-full mb-6 tracking-wider shadow-sm">
                METODE PENGAMBILAN KEPUTUSAN
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-6 text-saku-dark">
                Apa itu Metode <span class="text-saku-primary">SAW</span>?
            </h1>
            <p class="text-lg md:text-xl text-saku-muted leading-relaxed mb-6 font-medium">
                SAW (Simple Additive Weighting) adalah metode sistem pendukung keputusan yang membantu kamu memilih pilihan terbaik berdasarkan beberapa kriteria yang penting.
            </p>
            <p class="text-base md:text-lg text-saku-muted leading-relaxed font-medium">
                Dalam konteks SAKU, metode ini menghitung skor untuk setiap menu makanan berdasarkan kriteria seperti harga, nilai gizi, jarak warung, dan lainnya. Menu dengan skor tertinggi adalah rekomendasi terbaik untukmu—sesuai budget dan kebutuhan nutrisimu!
            </p>
        </div>

        {{-- Kriteria Section --}}
        <div class="max-w-5xl mx-auto mb-16">
            <h2 class="text-3xl font-extrabold text-saku-dark mb-8 text-center">
                Kriteria yang Digunakan
            </h2>
            
            @if($criteria->isEmpty())
                <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                    <p class="text-saku-muted">Belum ada kriteria yang ditambahkan oleh admin.</p>
                </div>
            @else
                <div class="grid md:grid-cols-2 gap-6">
                    @foreach($criteria as $criterion)
                        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 {{ $criterion->tipe === 'benefit' ? 'border-green-500' : 'border-red-500' }} hover:shadow-xl transition duration-300">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="text-xl font-bold text-saku-dark mb-1">{{ $criterion->nama_kriteria }}</h3>
                                    <p class="text-sm text-saku-muted font-semibold">Kode: {{ $criterion->kode }}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $criterion->tipe === 'benefit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $criterion->tipe === 'benefit' ? 'Benefit' : 'Cost' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                <span class="text-sm text-saku-muted font-semibold">Bobot Prioritas:</span>
                                <span class="text-2xl font-extrabold text-saku-primary">{{ number_format($criterion->bobot * 100, 1) }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6 bg-white rounded-xl shadow-md p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-saku-muted font-semibold">
                            <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                            <strong>Benefit:</strong> Semakin tinggi nilainya, semakin baik (contoh: kandungan gizi)
                        </p>
                        <p class="text-sm text-saku-muted font-semibold">
                            <span class="inline-block w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                            <strong>Cost:</strong> Semakin rendah nilainya, semakin baik (contoh: jarak tempuh)
                        </p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Cara Kerja Section --}}
        <div class="max-w-4xl mx-auto mb-16">
            <h2 class="text-3xl font-extrabold text-saku-dark mb-8 text-center">
                Bagaimana Cara Kerjanya?
            </h2>
            
            <div class="space-y-6">
                {{-- Step 1 --}}
                <div class="bg-white rounded-2xl shadow-lg p-6 flex items-start space-x-4 hover:shadow-xl transition duration-300">
                    <div class="flex-shrink-0 w-12 h-12 bg-saku-accent rounded-full flex items-center justify-center text-white font-extrabold text-xl">
                        1
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-saku-dark mb-2">Anda Masukkan Budget</h3>
                        <p class="text-saku-muted leading-relaxed">
                            Kamu input berapa budget maksimal yang tersedia untuk makan hari ini. Misalnya: Rp 15.000.
                        </p>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="bg-white rounded-2xl shadow-lg p-6 flex items-start space-x-4 hover:shadow-xl transition duration-300">
                    <div class="flex-shrink-0 w-12 h-12 bg-saku-accent rounded-full flex items-center justify-center text-white font-extrabold text-xl">
                        2
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-saku-dark mb-2">Sistem Menyaring Menu</h3>
                        <p class="text-saku-muted leading-relaxed">
                            Sistem otomatis menyaring semua menu yang harganya tidak melebihi budget kamu. Menu di atas budget langsung dikeluarkan.
                        </p>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="bg-white rounded-2xl shadow-lg p-6 flex items-start space-x-4 hover:shadow-xl transition duration-300">
                    <div class="flex-shrink-0 w-12 h-12 bg-saku-accent rounded-full flex items-center justify-center text-white font-extrabold text-xl">
                        3
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-saku-dark mb-2">Sistem Menghitung Skor</h3>
                        <p class="text-saku-muted leading-relaxed">
                            Setiap menu yang lolos dihitung skornya berdasarkan semua kriteria yang sudah ditentukan (gizi, jarak, dll). Kriteria yang lebih penting punya bobot lebih besar.
                        </p>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="bg-white rounded-2xl shadow-lg p-6 flex items-start space-x-4 hover:shadow-xl transition duration-300">
                    <div class="flex-shrink-0 w-12 h-12 bg-saku-accent rounded-full flex items-center justify-center text-white font-extrabold text-xl">
                        4
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-saku-dark mb-2">Anda Dapat Rekomendasi</h3>
                        <p class="text-saku-muted leading-relaxed">
                            Menu dengan skor tertinggi ditampilkan paling atas sebagai rekomendasi terbaik. Kamu tinggal pilih dan makan!
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CTA Section --}}
        <div class="max-w-3xl mx-auto text-center">
            <div class="bg-gradient-to-r from-saku-accent to-saku-primary rounded-3xl shadow-2xl p-10">
                <h2 class="text-3xl font-extrabold text-white mb-4">
                    Siap Mencoba Sistem Cerdas Ini?
                </h2>
                <p class="text-white text-lg mb-8 opacity-90">
                    Dapatkan rekomendasi menu personal yang sesuai dengan budget dan kebutuhan nutrisimu sekarang juga!
                </p>
                @auth
                    @if(Auth::user()->role === 'mahasiswa')
                        <a href="{{ route('student.dashboard') }}" class="inline-block px-8 py-4 bg-white text-saku-primary rounded-xl font-bold text-lg shadow-lg hover:bg-saku-light transform hover:-translate-y-1 transition duration-300">
                            Mulai Hitung Budget
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="inline-block px-8 py-4 bg-white text-saku-primary rounded-xl font-bold text-lg shadow-lg hover:bg-saku-light transform hover:-translate-y-1 transition duration-300">
                            Dashboard Admin
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="inline-block px-8 py-4 bg-white text-saku-primary rounded-xl font-bold text-lg shadow-lg hover:bg-saku-light transform hover:-translate-y-1 transition duration-300">
                        Coba Sekarang
                    </a>
                @endauth
            </div>
        </div>

    </main>

    {{-- Footer --}}
    <footer class="container mx-auto px-6 py-8 mt-16 border-t border-gray-300">
        <div class="text-center text-saku-muted text-sm">
            <p>&copy; 2026 SAKU. Sistem Pendukung Keputusan Pemilihan Menu Mahasiswa.</p>
        </div>
    </footer>

</body>
</html>
