@extends('admin.layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Mobile Friendly -->
    <div class="mb-6 md:mb-8 bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-4 md:p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-1 md:mb-2">Manajemen User</h1>
                <p class="text-orange-100 text-sm md:text-base">
                    <i class="fas fa-users mr-2"></i>Kelola hak akses dan pengguna sistem
                </p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.users.create') }}" 
                   class="bg-white text-orange-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition flex items-center text-sm font-semibold shadow-md">
                    <i class="fas fa-plus mr-2"></i>Tambah User
                </a>
                <div class="hidden md:block">
                    <i class="fas fa-id-card text-6xl opacity-30"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Cari nama atau email..." 
                       value="{{ request('search') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
            <div class="w-48">
                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>Kasir</option>
                </select>
            </div>
            <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
            @if(request()->has('search') || request()->has('role'))
            <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-times mr-2"></i>Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($users as $index => $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($user->photo)
                                    @php
                                        $photoUrl = null;
                                        if (Storage::disk('public')->exists($user->photo)) {
                                            $photoUrl = asset('storage/' . $user->photo);
                                        }
                                    @endphp
                                    @if($photoUrl)
                                        <img src="{{ $photoUrl }}" alt="{{ $user->name }}" 
                                             class="w-8 h-8 rounded-full object-cover mr-3" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random';">
                                    @else
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-orange-600"></i>
                                        </div>
                                    @endif
                                @else
                                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-orange-600"></i>
                                    </div>
                                @endif
                                <span class="font-medium">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">Admin</span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Kasir</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="toggleActive({{ $user->id }})" 
                                    class="px-2 py-1 rounded-full text-xs font-medium transition {{ $user->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}" 
                                   class="bg-blue-100 text-blue-600 p-2 rounded-lg hover:bg-blue-200 transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="resetPassword({{ $user->id }})" 
                                        class="bg-yellow-100 text-yellow-600 p-2 rounded-lg hover:bg-yellow-200 transition" title="Reset Password">
                                    <i class="fas fa-key"></i>
                                </button>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirmDelete()" 
                                            class="bg-red-100 text-red-600 p-2 rounded-lg hover:bg-red-200 transition" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-users text-5xl mb-3 text-gray-300"></i>
                            <p class="text-lg">Tidak ada user</p>
                            <a href="{{ route('admin.users.create') }}" class="inline-block mt-3 text-orange-600 hover:text-orange-700">
                                <i class="fas fa-plus mr-1"></i> Tambah User
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="mt-6">
        {{ $users->links() }}
    </div>
    @endif
</div>

<!-- Reset Password Modal -->
<div id="resetPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
        <div class="p-5 border-b bg-gradient-to-r from-orange-600 to-orange-500 text-white rounded-t-xl">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-key mr-2"></i>
                    Reset Password
                </h2>
                <button onclick="closeResetModal()" class="text-white hover:text-yellow-200 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <form id="resetPasswordForm">
                @csrf
                <input type="hidden" id="resetUserId" name="user_id">
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-lock text-yellow-600 mr-1"></i>
                        Password Baru
                    </label>
                    <input type="password" id="newPassword" name="password" 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition"
                           placeholder="Minimal 6 karakter"
                           required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-check-circle text-green-600 mr-1"></i>
                        Konfirmasi Password Baru
                    </label>
                    <input type="password" id="confirmPassword" name="password_confirmation" 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition"
                           placeholder="Ulangi password baru"
                           required>
                </div>
                
                <div class="bg-yellow-50 rounded-xl p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-yellow-600 text-lg mt-0.5"></i>
                        <div>
                            <p class="text-sm text-yellow-800 font-medium">Informasi</p>
                            <p class="text-xs text-yellow-700">Password akan direset dan user harus login dengan password baru.</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeResetModal()" 
                            class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl hover:bg-gray-300 transition font-semibold">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-orange-600 text-white py-3 rounded-xl hover:bg-orange-700 transition font-semibold shadow-lg">
                        <i class="fas fa-save mr-2"></i>Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ============================================
// TOGGLE ACTIVE STATUS
// ============================================
function toggleActive(userId) {
    fetch(`/admin/users/${userId}/toggle-active`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Status user berhasil diubah',
                timer: 1500,
                showConfirmButton: false
            });
            setTimeout(() => location.reload(), 1500);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message || 'Terjadi kesalahan',
                confirmButtonColor: '#f97316'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat mengubah status',
            confirmButtonColor: '#f97316'
        });
    });
}

// ============================================
// RESET PASSWORD FUNCTIONS
// ============================================

// Open reset password modal
function resetPassword(userId) {
    document.getElementById('resetUserId').value = userId;
    document.getElementById('resetPasswordModal').classList.remove('hidden');
    document.getElementById('resetPasswordModal').classList.add('flex');
    
    // Reset form
    document.getElementById('resetPasswordForm').reset();
    
    // Focus ke input password
    setTimeout(() => {
        document.getElementById('newPassword').focus();
    }, 100);
}

// Close reset password modal
function closeResetModal() {
    document.getElementById('resetPasswordModal').classList.add('hidden');
    document.getElementById('resetPasswordModal').classList.remove('flex');
    document.getElementById('resetPasswordForm').reset();
}

// Handle form submit
document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const userId = document.getElementById('resetUserId').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Validasi password match
    if (newPassword !== confirmPassword) {
        Swal.fire({
            icon: 'error',
            title: 'Password Tidak Cocok!',
            text: 'Password dan konfirmasi password harus sama.',
            confirmButtonColor: '#f97316'
        });
        return;
    }
    
    // Validasi minimal panjang password
    if (newPassword.length < 6) {
        Swal.fire({
            icon: 'error',
            title: 'Password Terlalu Pendek!',
            text: 'Password minimal 6 karakter.',
            confirmButtonColor: '#f97316'
        });
        return;
    }
    
    // Tampilkan loading
    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang mereset password',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Kirim request reset password
    fetch(`/admin/users/${userId}/reset-password`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            password: newPassword,
            password_confirmation: confirmPassword
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            closeResetModal();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message,
                confirmButtonColor: '#f97316'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat mereset password',
            confirmButtonColor: '#f97316'
        });
    });
});

// ============================================
// CONFIRM DELETE
// ============================================
function confirmDelete() {
    Swal.fire({
        title: 'Hapus User?',
        text: 'Tindakan ini tidak dapat dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        return result.isConfirmed;
    });
}

// Close modal when clicking outside
document.getElementById('resetPasswordModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeResetModal();
    }
});

// Enter key support
document.getElementById('newPassword')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('confirmPassword').focus();
    }
});

document.getElementById('confirmPassword')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('resetPasswordForm').dispatchEvent(new Event('submit'));
    }
});
</script>
@endpush
@endsection