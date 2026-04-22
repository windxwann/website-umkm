@extends('admin.layouts.app')

@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-orange-600 to-orange-500">
            <h2 class="text-2xl font-bold text-white">
                <i class="fas fa-edit mr-2"></i>Edit Kategori: {{ $category->name }}
            </h2>
        </div>

        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Nama Kategori -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                    <textarea name="description" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $category->description) }}</textarea>
                </div>

                <!-- Gambar Saat Ini -->
                @if($category->image)
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Gambar Saat Ini</label>
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                        @php
                            $imageUrl = null;
                            $imagePath = str_starts_with($category->image, 'categories/') ? $category->image : 'categories/' . $category->image;
                            if (Storage::disk('public')->exists($imagePath)) {
                                $imageUrl = asset('storage/' . $imagePath);
                            } elseif (Storage::disk('public')->exists($category->image)) {
                                $imageUrl = asset('storage/' . $category->image);
                            }
                        @endphp
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $category->name }}" 
                                 class="w-20 h-20 object-cover rounded-lg" onerror="this.onerror=null; this.src='https://via.placeholder.com/150?text=No+Image';">
                        @endif
                        <div>
                            <p class="text-sm text-gray-600">{{ basename($category->image) }}</p>
                            <label class="inline-flex items-center mt-2">
                                <input type="checkbox" name="remove_image" value="1" class="mr-2">
                                <span class="text-sm text-red-600">Hapus gambar</span>
                            </label>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Upload Gambar Baru -->
                <div x-data="{ imagePreview: '' }">
                    <label class="block text-gray-700 font-semibold mb-2">Gambar Baru (Kosongkan jika tidak ingin mengubah)</label>
                    
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
                                <p class="text-gray-500">Preview gambar baru akan muncul di sini</p>
                            </div>
                        </template>
                    </div>

                    <!-- Upload Button -->
                    <div class="flex items-center justify-center w-full">
                        <label class="w-full flex flex-col items-center px-4 py-6 bg-white rounded-lg border-2 border-dashed border-gray-300 cursor-pointer hover:border-orange-500 transition">
                            <i class="fas fa-image text-3xl text-gray-400 mb-2"></i>
                            <span class="text-gray-600 font-semibold">Klik untuk upload gambar baru</span>
                            <span class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG (Maks. 2MB)</span>
                            <input type="file" x-ref="fileInput" name="image" accept="image/*" class="hidden"
                                   @change="imagePreview = URL.createObjectURL($event.target.files[0])">
                        </label>
                    </div>
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statistik -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-700 mb-3">Informasi Kategori</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Slug:</span>
                            <span class="ml-2 font-mono">{{ $category->slug }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Total Produk:</span>
                            <span class="ml-2 font-semibold text-orange-600">{{ $category->products_count ?? $category->products()->count() }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Dibuat:</span>
                            <span class="ml-2">{{ $category->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Diupdate:</span>
                            <span class="ml-2">{{ $category->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Warning jika ada produk -->
                @if($category->products()->count() > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Perhatian:</strong> Kategori ini memiliki {{ $category->products()->count() }} produk. 
                                Menghapus kategori akan mempengaruhi produk-produk tersebut.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" class="flex-1 bg-orange-600 text-white py-3 rounded-lg hover:bg-orange-700 transition font-semibold">
                    <i class="fas fa-save mr-2"></i>Update Kategori
                </button>
                <a href="{{ route('admin.categories.index') }}" 
                   class="flex-1 bg-gray-500 text-white py-3 rounded-lg hover:bg-gray-600 transition text-center font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection