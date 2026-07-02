<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Menu - SAKU</title>
    
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
            <a href="{{ route('tentang-saw') }}" class="text-saku-muted hover:text-saku-dark transition duration-200">Tentang SAW</a>
            <a href="{{ route('daftar-menu') }}" class="text-saku-dark border-b-2 border-saku-accent pb-1">Daftar Menu</a>
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
        
        {{-- Header Section --}}
        <div class="max-w-4xl mx-auto text-center mb-12">
            <div class="inline-block px-4 py-1.5 bg-saku-dark text-saku-light text-xs font-bold rounded-full mb-6 tracking-wider shadow-sm">
                KATALOG MENU TERSEDIA
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-6 text-saku-dark">
                Daftar Menu <span class="text-saku-primary">Pilihan</span>
            </h1>
            <p class="text-lg text-saku-muted leading-relaxed font-medium">
                Jelajahi semua menu makanan yang tersedia di sekitar kampus. Untuk mendapatkan rekomendasi personal sesuai budget, silakan masuk terlebih dahulu.
            </p>
        </div>

        {{-- Search Bar --}}
        <div class="max-w-2xl mx-auto mb-10">
            <form action="{{ route('daftar-menu') }}" method="GET" class="relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search ?? '' }}"
                    placeholder="Cari nama menu atau vendor..." 
                    class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 focus:border-saku-accent focus:outline-none text-base font-medium bg-white shadow-md"
                >
                <button 
                    type="submit"
                    class="absolute right-2 top-1/2 -translate-y-1/2 px-6 py-2 bg-saku-accent text-white rounded-lg font-bold hover:bg-saku-primary transition duration-300"
                >
                    Cari
                </button>
            </form>
            @if($search)
                <div class="mt-4 text-center">
                    <span class="text-sm text-saku-muted">Menampilkan hasil untuk: <strong class="text-saku-dark">"{{ $search }}"</strong></span>
                    <a href="{{ route('daftar-menu') }}" class="ml-3 text-sm text-saku-accent font-bold hover:underline">Hapus Filter</a>
                </div>
            @endif
        </div>

        {{-- Menu Grid --}}
        @if($menus->isEmpty())
            <div class="max-w-2xl mx-auto text-center py-16">
                <div class="bg-white rounded-2xl shadow-lg p-12">
                    <div class="text-6xl mb-4">🍽️</div>
                    <h3 class="text-2xl font-bold text-saku-dark mb-3">Belum Ada Menu</h3>
                    <p class="text-saku-muted">
                        @if($search)
                            Maaf, tidak ada menu yang cocok dengan pencarian "{{ $search }}". Coba kata kunci lain!
                        @else
                            Belum ada menu yang tersedia saat ini. Silakan cek kembali nanti.
                        @endif
                    </p>
                </div>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @foreach($menus as $menu)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition duration-300">
                        {{-- Image --}}
                        <div class="h-48 bg-gradient-to-br from-saku-light to-saku-accent overflow-hidden">
                            @if($menu->image_url)
                                <img src="{{ $menu->image_url }}" alt="{{ $menu->menu_name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-6xl">
                                    🍛
                                </div>
                            @endif
                        </div>
                        
                        {{-- Content --}}
                        <div class="p-6">
                            <div class="mb-3">
                                <span class="text-xs font-bold text-saku-muted uppercase tracking-wider">{{ $menu->vendor_name }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-saku-dark mb-3">{{ $menu->menu_name }}</h3>
                            
                            @if($menu->description)
                                <p class="text-sm text-saku-muted mb-4 line-clamp-2 leading-relaxed">
                                    {{ Str::limit($menu->description, 80) }}
                                </p>
                            @endif
                            
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div>
                                    <span class="text-xs text-saku-muted font-semibold block mb-1">Harga</span>
                                    <span class="text-2xl font-extrabold text-saku-primary">
                                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($menus->hasPages())
                <div class="flex justify-center">
                    <div class="bg-white rounded-xl shadow-md px-6 py-4">
                        {{ $menus->links('pagination::tailwind') }}
                    </div>
                </div>
            @endif
        @endif

        {{-- CTA Banner --}}
        <div class="max-w-4xl mx-auto mt-16">
            <div class="bg-gradient-to-r from-saku-primary to-saku-accent rounded-3xl shadow-2xl p-10 text-center">
                <h2 class="text-3xl font-extrabold text-white mb-4">
                    Ingin Rekomendasi Personal?
                </h2>
                <p class="text-white text-lg mb-6 opacity-90">
                    Masuk sekarang dan dapatkan rekomendasi menu terbaik yang sesuai dengan budget dan kebutuhan nutrisimu!
                </p>
                @auth
                    @if(Auth::user()->role === 'mahasiswa')
                        <a href="{{ route('student.dashboard') }}" class="inline-block px-8 py-4 bg-white text-saku-primary rounded-xl font-bold text-lg shadow-lg hover:bg-saku-light transform hover:-translate-y-1 transition duration-300">
                            Hitung Budget Sekarang
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="inline-block px-8 py-4 bg-white text-saku-primary rounded-xl font-bold text-lg shadow-lg hover:bg-saku-light transform hover:-translate-y-1 transition duration-300">
                            Dashboard Admin
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="inline-block px-8 py-4 bg-white text-saku-primary rounded-xl font-bold text-lg shadow-lg hover:bg-saku-light transform hover:-translate-y-1 transition duration-300">
                        Masuk di Sini
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
