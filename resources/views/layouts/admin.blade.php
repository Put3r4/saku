<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin SAKU') - Panel Administrator</title>
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
<body class="bg-gray-50 text-saku-dark antialiased flex h-screen overflow-hidden">

    <aside class="w-64 bg-saku-dark text-saku-light flex flex-col shadow-2xl z-20 hidden md:flex">
        <div class="h-16 flex items-center justify-center border-b border-saku-muted/30">
            <span class="text-2xl font-extrabold tracking-widest text-white">SAKU<span class="text-saku-accent">.</span>ADMIN</span>
        </div>
       <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
    <a href="{{ route('home') }}" class="flex items-center px-4 py-3 text-saku-light hover:bg-saku-muted/20 hover:text-white rounded-lg font-semibold transition">
        <span>&larr; Home</span>
    </a>
    <div class="border-b border-saku-muted/20 my-2"></div>

    <a href="{{ route('admin.dashboard') }}" 
       class="flex items-center px-4 py-3 rounded-lg font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-saku-accent text-white shadow-md' : 'text-saku-light hover:bg-saku-muted/20 hover:text-white' }}">
        <span>Dasbor Utama</span>
    </a>

    <a href="{{ route('admin.criteria.index') }}" 
       class="flex items-center px-4 py-3 rounded-lg font-semibold transition {{ request()->routeIs('admin.criteria.*') ? 'bg-saku-accent text-white shadow-md' : 'text-saku-light hover:bg-saku-muted/20 hover:text-white' }}">
        <span>Kelola Kriteria & Bobot</span>
    </a>

    <a href="{{ route('admin.menu.index') }}" 
       class="flex items-center px-4 py-3 rounded-lg font-semibold transition {{ request()->routeIs('admin.menu.*') ? 'bg-saku-accent text-white shadow-md' : 'text-saku-light hover:bg-saku-muted/20 hover:text-white' }}">
        <span>Kelola Menu</span>
    </a>

    <a href="{{ route('admin.matrix.index') }}" 
       class="flex items-center px-4 py-3 rounded-lg font-semibold transition {{ request()->routeIs('admin.matrix.*') ? 'bg-saku-accent text-white shadow-md' : 'text-saku-light hover:bg-saku-muted/20 hover:text-white' }}">
        <span>Input Matriks</span>
    </a>
    
</nav>
        <div class="p-4 border-t border-saku-muted/30">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-2 bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white rounded-lg font-bold transition">
                    Keluar Sistem
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 z-10">
            <h2 class="text-xl font-bold text-saku-dark">@yield('header')</h2>
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-saku-light flex items-center justify-center text-saku-primary font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <span class="font-semibold text-sm">{{ Auth::user()->name }}</span>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8">
            @yield('content')
        </div>
    </main>

</body>
</html>