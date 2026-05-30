@extends('admin.layouts.app')

@section('title', 'Tambah Pengguna')
@section('page-title', 'Pengguna')

@section('content')
<div class="max-w-xl mx-auto">
    <!-- Breadcrumb & Header - Compact -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" 
           class="p-2 bg-white border border-slate-100 text-slate-400 hover:text-slate-600 rounded-xl transition-all shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-lg font-black text-slate-900 tracking-tight leading-none">Tambah Pengguna</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Buat User Baru</p>
        </div>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6">
            
            <!-- Nama & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <div class="relative group">
                        <i data-lucide="user" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Email <span class="text-rose-500">*</span></label>
                    <div class="relative group">
                        <i data-lucide="mail" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                    </div>
                </div>
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Password <span class="text-rose-500">*</span></label>
                    <div class="relative group">
                        <i data-lucide="lock" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="password" name="password" required class="w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Konfirmasi Password <span class="text-rose-500">*</span></label>
                    <div class="relative group">
                        <i data-lucide="lock" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="password" name="password_confirmation" required class="w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                    </div>
                </div>
            </div>

            <!-- Role & Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Role <span class="text-rose-500">*</span></label>
                    <div class="relative group">
                        <i data-lucide="shield" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <select name="role" required class="w-full pl-11 pr-10 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all appearance-none cursor-pointer">
                            <option value="cashier">Kasir</option>
                            <option value="admin">Admin</option>
                        </select>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"></i>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">No. Telepon</label>
                    <div class="relative group">
                        <i data-lucide="phone" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-orange-600 text-white rounded-[1.5rem] text-[11px] font-black uppercase tracking-widest shadow-xl shadow-orange-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 group">
                <i data-lucide="save" class="w-4 h-4 group-hover:rotate-12 transition-transform"></i>
                Simpan Pengguna
            </button>
        </div>
    </form>
</div>
@endsection
