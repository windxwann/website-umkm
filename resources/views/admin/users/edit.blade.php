@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-orange-600 to-orange-500">
                    <h2 class="text-2xl font-bold text-white">
                    <i class="fas fa-user-edit mr-2"></i>Edit User
                </h2>
            </div>
            
            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Role</label>
                    <select name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="cashier" {{ old('role', $user->role) == 'cashier' ? 'selected' : '' }}>Kasir</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                @php
                    $photoPreviewUrl = '';
                    if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                        $photoPreviewUrl = asset('storage/' . $user->photo);
                    }
                @endphp
                <div class="mb-6" x-data="{ photoPreview: '{{ $photoPreviewUrl }}' }">
                    <label class="block text-gray-700 font-semibold mb-2">Foto</label>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div x-show="!photoPreview" class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-3xl text-gray-400"></i>
                            </div>
                            <img x-show="photoPreview" :src="photoPreview" class="w-20 h-20 rounded-full object-cover">
                        </div>
                        <div class="flex-1">
                            <input type="file" name="photo" accept="image/*"
                                   @change="photoPreview = URL.createObjectURL($event.target.files[0])"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah foto</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700">
                        <i class="fas fa-save mr-2"></i>Update
                    </button>
                    <a href="{{ route('admin.users.index') }}" 
                       class="flex-1 bg-gray-500 text-white py-2 rounded-lg hover:bg-gray-600 text-center">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection