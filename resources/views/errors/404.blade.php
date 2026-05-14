<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
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
                    },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;600;800;900&display=swap');
        body { font-family: 'Figtree', sans-serif; }
        
        /* Pola latar belakang (Background Pattern) */
        .bg-pattern {
            background-color: #E1DBCB;
            background-image: radial-gradient(#D68438 0.5px, transparent 0.5px), radial-gradient(#D68438 0.5px, #E1DBCB 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            background-opacity: 0.1;
        }
    </style>
</head>
<body class="bg-pattern antialiased min-h-screen flex items-center justify-center relative overflow-hidden selection:bg-saku-accent selection:text-white">

    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/40 blur-3xl rounded-full rounded-full animate-pulse-slow"></div>

    <div class="relative z-10 text-center px-4 max-w-2xl mx-auto flex flex-col items-center">
        
        <div class="animate-float mb-4">
            <h1 class="text-[10rem] md:text-[14rem] font-black text-saku-dark leading-none drop-shadow-2xl">
                4<span class="text-saku-accent">0</span>4
            </h1>
        </div>

        <h2 class="text-3xl md:text-4xl font-extrabold text-saku-primary mb-4 tracking-tight">
            Error Not Found
        </h2>
        
        <p class="text-saku-dark/80 font-medium text-lg md:text-xl mb-10 max-w-md mx-auto">
            Ups! Sepertinya Anda tersesat. Halaman yang Anda cari mungkin telah dihapus, dipindahkan, atau tidak pernah ada.
        </p>

        <a href="{{ route('home') }}" 
           class="group relative inline-flex items-center justify-center px-8 py-4 font-bold text-white transition-all duration-300 bg-saku-dark rounded-full hover:bg-saku-primary hover:shadow-xl hover:shadow-saku-primary/30 transform hover:-translate-y-1">
            <svg class="w-5 h-5 mr-2 transform transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Beranda
        </a>

    </div>

</body>
</html>