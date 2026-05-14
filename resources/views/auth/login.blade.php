<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SAKU</title>
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
<body class="bg-saku-light text-saku-dark antialiased min-h-screen flex items-center justify-center selection:bg-saku-accent selection:text-white">

    <div class="w-full max-w-md bg-white p-8 sm:p-10 rounded-[2rem] shadow-2xl border-t-8 border-saku-accent mx-4">
        
        <div class="text-center mb-8">
            <a href="/" class="text-4xl font-extrabold tracking-wider text-saku-primary inline-block mb-2">
                SAKU<span class="text-saku-accent">.</span>
            </a>
            <p class="text-saku-muted font-medium text-sm">Masuk untuk mengelola anggaran makanmu.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf <button type="submit" class="w-full py-4 bg-saku-dark text-saku-light font-bold rounded-xl shadow-lg hover:bg-saku-primary transition duration-300">
        Masuk ke Dasbor
    </button>
</form> 
            
            <div class="mb-5">
                <label for="email" class="block text-sm font-bold text-saku-dark mb-2">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-saku-accent focus:ring-2 focus:ring-saku-accent/20 outline-none transition duration-200 text-saku-dark bg-gray-50 focus:bg-white">
            </div>

            <div class="mb-8">
                <label for="password" class="block text-sm font-bold text-saku-dark mb-2">Kata Sandi</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-saku-accent focus:ring-2 focus:ring-saku-accent/20 outline-none transition duration-200 text-saku-dark bg-gray-50 focus:bg-white">
            </div>

            <button type="submit" 
                class="w-full py-4 bg-saku-dark text-saku-light font-bold rounded-xl shadow-lg hover:bg-saku-primary hover:shadow-xl transform hover:-translate-y-0.5 transition duration-300">
                Masuk ke Dasbor
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="/" class="text-sm text-saku-muted hover:text-saku-accent font-semibold transition">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </div>

</body>
</html>