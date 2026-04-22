@extends('admin.layouts.app')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-orange-600 to-orange-500">
            <h2 class="text-2xl font-bold text-white">
                <i class="fas fa-plus-circle mr-2"></i>Tambah Kategori Baru
            </h2>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- Nama Kategori -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('name') border-red-500 @enderror"
                           placeholder="Contoh: Seafood, Masakan Sunda, dll">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                    <textarea name="description" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                              placeholder="Deskripsi kategori...">{{ old('description') }}</textarea>
                </div>

                <!-- Upload Gambar -->
                <div x-data="{ imagePreview: '' }">
                    <label class="block text-gray-700 font-semibold mb-2">Gambar Kategori</label>
                    
                    <!-- Preview Area -->
                    <div class="mb-4 flex justify-center">
                        <template x-if="imagePreview">
                            <div class="relative">
                                <img :src="imagePreview" class="max-h-48 rounded-lg shadow-lg">
                                <button type="button" @click="imagePreview = ''; $refs.fileInput.value = ''" 
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        </template>
                        <template x-if="!imagePreview">
                            <div class="w-full max-w-md h-48 bg-gray-100 rounded-lg flex flex-col items-center justify-center border-2 border-dashed border-gray-300">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-500">Preview gambar akan muncul di sini</p>
                            </div>
                        </template>
                    </div>

                    <!-- Upload Button -->
                    <div class="flex items-center justify-center w-full">
                        <label class="w-full flex flex-col items-center px-4 py-6 bg-white rounded-lg border-2 border-dashed border-gray-300 cursor-pointer hover:border-orange-500 transition">
                            <i class="fas fa-image text-3xl text-gray-400 mb-2"></i>
                            <span class="text-gray-600 font-semibold">Klik untuk upload gambar</span>
                            <span class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG (Maks. 2MB)</span>
                            <input type="file" x-ref="fileInput" name="image" accept="image/*" class="hidden"
                                   @change="imagePreview = URL.createObjectURL($event.target.files[0])">
                        </label>
                    </div>
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informasi -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Kategori yang sudah memiliki produk tidak dapat dihapus. Pastikan tidak ada produk sebelum menghapus kategori.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" class="flex-1 bg-orange-600 text-white py-3 rounded-lg hover:bg-orange-700 transition font-semibold">
                    <i class="fas fa-save mr-2"></i>Simpan Kategori
                </button>
                <a href="{{ route('admin.categories.index') }}" 
                   class="flex-1 bg-gray-500 text-white py-3 rounded-lg hover:bg-gray-600 transition text-center font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection