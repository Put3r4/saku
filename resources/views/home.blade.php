<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAKU - Pilihan Cerdas Perut Mahasiswa</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        saku: {
                            dark: '#2A324A',     /* Teks Utama */
                            muted: '#7A82A6',    /* Teks Sekunder */
                            light: '#E1DBCB',    /* Background Utama */
                            accent: '#D68438',   /* Tombol CTA */
                            primary: '#8D4F37',  /* Highlight / Hover */
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

    <nav class="container mx-auto px-6 py-5 flex justify-between items-center">
        <div class="text-3xl font-extrabold tracking-wider text-saku-primary">
            SAKU<span class="text-saku-accent">.</span>
        </div>
        <div class="hidden md:flex space-x-10 text-base font-semibold">
            <a href="#" class="text-saku-dark border-b-2 border-saku-accent pb-1">Beranda</a>
            <a href="#" class="text-saku-muted hover:text-saku-dark transition duration-200">Tentang SAW</a>
            <a href="#" class="text-saku-muted hover:text-saku-dark transition duration-200">Daftar Menu</a>
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

    <main class="container mx-auto px-6 py-16 md:py-24 flex flex-col-reverse md:flex-row items-center min-h-[85vh]">
        
        <div class="w-full md:w-1/2 mt-12 md:mt-0 md:pr-12">
            <div class="inline-block px-4 py-1.5 bg-saku-dark text-saku-light text-xs font-bold rounded-full mb-6 tracking-wider shadow-sm">
                METODE SIMPLE ADDITIVE WEIGHTING
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6 text-saku-dark">
                Makan Enak, <br>
                Gizi Pas, <span class="text-saku-primary block mt-2">Dompet Aman.</span>
            </h1>
            <p class="text-lg md:text-xl text-saku-muted mb-10 leading-relaxed max-w-lg font-medium">
                Jangan biarkan akhir bulan menyiksa perutmu. SAKU merekomendasikan menu harian terbaik yang sesuai dengan sisa anggaran, tanpa mengorbankan asupan nutrisi mahasiswa.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="#" class="px-8 py-4 bg-saku-accent text-white text-center rounded-xl font-bold text-lg shadow-lg hover:bg-saku-primary transform hover:-translate-y-1 transition duration-300">
                    Mulai Hitung Budget
                </a>
                <a href="#" class="px-8 py-4 bg-white text-saku-dark text-center rounded-xl font-bold text-lg shadow-md border border-gray-200 hover:border-saku-muted transition duration-300">
                    Pelajari Sistem
                </a>
            </div>
        </div>

        <div class="w-full md:w-1/2 flex justify-center">
            <div class="relative w-full max-w-lg aspect-square bg-white bg-opacity-40 backdrop-blur-sm rounded-[3rem] overflow-hidden flex flex-col items-center justify-center border-4 border-white shadow-2xl">
                
                <div class="bg-white p-5 rounded-2xl shadow-lg w-3/4 mb-4 transform -rotate-3 border-l-4 border-saku-accent z-10">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold text-saku-dark">Nasi Ayam Bakar</span>
                        <span class="text-xs font-bold bg-saku-light text-saku-primary px-2 py-1 rounded">Rank 1</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                        <div class="bg-saku-accent h-2 rounded-full" style="width: 95%"></div>
                    </div>
                    <p class="text-xs text-saku-muted text-right">Skor: 0.95 (Anggaran Pas)</p>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-lg w-3/4 transform translate-x-4 rotate-2 opacity-80 border-l-4 border-saku-muted z-0">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold text-saku-dark">Soto Ayam Madura</span>
                        <span class="text-xs font-bold bg-gray-100 text-saku-muted px-2 py-1 rounded">Rank 2</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                        <div class="bg-saku-muted h-2 rounded-full" style="width: 82%"></div>
                    </div>
                    <p class="text-xs text-saku-muted text-right">Skor: 0.82</p>
                </div>
                
                <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-saku-accent rounded-full opacity-20 blur-3xl"></div>
                <div class="absolute top-10 -left-10 w-40 h-40 bg-saku-primary rounded-full opacity-10 blur-3xl"></div>
            </div>
        </div>
        
    </main>

</body>
</html>