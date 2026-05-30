@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Pengguna')

@section('content')
<!-- Actions & Stats - Compact -->
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
    <a href="{{ route('admin.users.create') }}" 
       class="flex items-center justify-center gap-2 px-4 py-2.5 bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-orange-600/20 hover:scale-[1.02] transition-all">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Tambah Pengguna
    </a>

    <form method="GET" action="{{ route('admin.users.index') }}" class="w-full lg:w-auto flex flex-wrap gap-2">
        <div class="relative w-full sm:w-64">
            <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" placeholder="Cari nama atau email..." 
                   value="{{ request('search') }}"
                   class="w-full pl-9 pr-3 py-2 bg-white border border-gray-100 rounded-xl text-[11px] font-bold focus:outline-none focus:ring-4 focus:ring-orange-500/5 transition-all shadow-sm">
        </div>

        <div class="relative w-full sm:w-36">
            <i data-lucide="shield" class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            <select name="role" class="w-full pl-9 pr-8 py-2 bg-white border border-gray-100 rounded-xl text-[11px] font-bold focus:outline-none focus:ring-4 focus:ring-orange-500/5 transition-all appearance-none cursor-pointer">
                <option value="">Semua Role</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>Kasir</option>
            </select>
        </div>

        @if(request('search') || request('role'))
        <a href="{{ route('admin.users.index') }}" 
           class="flex items-center justify-center p-2 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition-all shrink-0 shadow-sm"
           title="Hapus Filter">
            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
        </a>
        @endif
    </form>
</div>

<!-- Stats Bar - Compact -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-8">
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600"><i data-lucide="users" class="w-4 h-4"></i></div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Total User</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $users->total() }}</p>
        </div>
    </div>
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600"><i data-lucide="shield-check" class="w-4 h-4"></i></div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Admin</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $users->where('role', 'admin')->count() }}</p>
        </div>
    </div>
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600"><i data-lucide="user" class="w-4 h-4"></i></div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Kasir</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $users->where('role', 'cashier')->count() }}</p>
        </div>
    </div>
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
        <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600"><i data-lucide="clock" class="w-4 h-4"></i></div>
        <div>
            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Aktif</p>
            <p class="text-xs font-black text-slate-900 leading-none mt-0.5">{{ $users->where('is_active', true)->count() }}</p>
        </div>
    </div>
</div>

<!-- Users Table - Compact Design -->
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-8">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em]">Pengguna</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em]">Role</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em]">Status</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em] text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=f97316&color=fff" class="w-9 h-9 rounded-xl shadow-sm border border-slate-100">
                            <div>
                                <p class="text-xs font-black text-slate-900 leading-none">{{ $user->name }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest {{ $user->role === 'admin' ? 'bg-purple-50 text-purple-600' : 'bg-emerald-50 text-emerald-600' }}">
                            {{ strtoupper($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <button onclick="toggleActive({{ $user->id }})" 
                                class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest transition-all {{ $user->is_active ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-rose-50 text-rose-600 hover:bg-rose-100' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.users.edit', $user) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="Edit"><i data-lucide="edit-3" class="w-3.5 h-3.5"></i></a>
                            <button onclick="resetPassword({{ $user->id }})" class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition-colors" title="Reset Password"><i data-lucide="key" class="w-3.5 h-3.5"></i></button>
                            @if($user->id !== auth()->id())
                                <button onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')" class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 transition-colors" title="Hapus"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                                <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-10 text-center text-[10px] font-black text-slate-300 uppercase tracking-widest">Belum ada user</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteUser(id, name) {
    Swal.fire({
        title: '<span class="font-black uppercase tracking-tighter text-xl text-rose-600">HAPUS USER?</span>',
        html: `<p class="text-gray-500 font-medium text-sm">User <b>${name}</b> akan dihapus permanen.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'YA, HAPUS',
        cancelButtonText: 'BATAL',
        borderRadius: '1.5rem'
    }).then((result) => {
        if (result.isConfirmed) document.getElementById('delete-form-' + id).submit();
    });
}
// Tambahkan fungsi toggleActive dan resetPassword yang sudah ada...
</script>
@endpush
