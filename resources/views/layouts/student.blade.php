<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SAKU') - Sistem Rekomendasi Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        saku: {
                            dark: '#2A324A',     /* Navy - Teks utama & Sidebar */
                            muted: '#7A82A6',    /* Steel - Teks sekunder */
                            light: '#E1DBCB',    /* Cream - Aksen latar / Hover */
                            accent: '#D68438',   /* Orange - Tombol / Sorotan */
                            primary: '#8D4F37',  /* Rust - Hover tombol */
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
<body class="bg-gray-50 text-saku-dark antialiased">

    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('student.dashboard') }}" class="text-2xl font-extrabold tracking-widest text-saku-dark">
                        SAKU<span class="text-saku-accent">.</span>
                    </a>
                    <span class="ml-4 text-sm text-saku-muted font-medium">Sistem Rekomendasi Menu</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-saku-muted hover:text-saku-dark transition">
                        Beranda
                    </a>
                    <a href="{{ route('student.history.index') }}" class="text-sm font-medium text-saku-muted hover:text-saku-dark transition">
                        📊 Riwayat
                    </a>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-saku-light flex items-center justify-center text-saku-primary font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="font-semibold text-sm">{{ Auth::user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-700 transition">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-12 bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-sm text-saku-muted">
                &copy; {{ date('Y') }} SAKU - Sistem Anggaran Kuliner yang Aman. Powered by SAW Algorithm.
            </p>
        </div>
    </footer>

</body>
</html>
