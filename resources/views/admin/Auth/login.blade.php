<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - {{ setting('restaurant_name', 'Dapoer Jiemas') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="antialiased flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-orange-600 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-600/20 p-3 mx-auto mb-4">
                <i data-lucide="utensils" class="text-white w-full h-full"></i>
            </div>
            <h1 class="text-xl font-black text-slate-900 uppercase tracking-widest">{{ setting('restaurant_name', 'Dapoer Jiemas') }}</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Admin Panel Login</p>
        </div>

        <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl">
            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-600 rounded-2xl text-[10px] font-black uppercase tracking-widest">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Email</label>
                    <div class="relative group">
                        <i data-lucide="mail" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-orange-500 transition-colors"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                    </div>
                </div>

                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Password</label>
                    <div class="relative group">
                        <i data-lucide="lock" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-orange-500 transition-colors"></i>
                        <input type="password" name="password" required
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-orange-600 text-white py-4 rounded-[1.5rem] text-[11px] font-black uppercase tracking-widest shadow-lg shadow-orange-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    Login Panel
                </button>
            </form>
        </div>
        
        <p class="text-center text-[9px] font-black text-slate-300 uppercase tracking-[0.2em] mt-8">
            &copy; {{ date('Y') }} {{ setting('restaurant_name', 'Dapoer Jiemas') }}
        </p>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
