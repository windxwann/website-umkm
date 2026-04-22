@extends('admin.layouts.app')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-orange-600 to-orange-500">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Edit Produk
            </h2>
        </div>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Produk -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $product->name) }}" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Harga (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" 
                           name="price" 
                           value="{{ old('price', $product->price) }}" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                    <textarea name="description" 
                              rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">{{ old('description', $product->description) }}</textarea>
                </div>

                <!-- Gambar Saat Ini -->
                @if($product->image)
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">Gambar Saat Ini</label>
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                        @php
                            $imagePath = $product->image;
                            if (!str_contains($imagePath, 'products/')) {
                                $imagePath = 'products/' . $imagePath;
                            }
                        @endphp
                        <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover rounded-lg" onerror="this.onerror=null; this.src='https://via.placeholder.com/150?text=No+Image';">
                        <div>
                            <p class="text-sm text-gray-600">{{ basename($product->image) }}</p>
                            <label class="inline-flex items-center mt-2">
                                <input type="checkbox" name="remove_image" value="1" class="mr-2">
                                <span class="text-sm text-red-600">Hapus gambar</span>
                            </label>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Upload Gambar Baru -->
                <div class="md:col-span-2" x-data="{ imagePreview: '' }">
                    <label class="block text-gray-700 font-semibold mb-2">Gambar Baru (Kosongkan jika tidak ingin mengubah)</label>
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <div x-show="!imagePreview" class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
                            </div>
                            <img x-show="imagePreview" :src="imagePreview" class="w-24 h-24 object-cover rounded-lg border-2 border-orange-500">
                        </div>
                        <div class="flex-1">
                            <input type="file" name="image" accept="image/*"
                                   @change="imagePreview = URL.createObjectURL($event.target.files[0])"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG | Maks: 2MB</p>
                        </div>
                    </div>
                </div>

                <!-- Status Tersedia -->
                <div class="md:col-span-2">
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" name="is_available" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }}
                               class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500">
                        <span class="text-gray-700">Produk tersedia</span>
                    </label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-yellow-700 transition font-semibold">
                    <i class="fas fa-save mr-2"></i> Update Produk
                </button>
                <a href="{{ route('admin.products.index') }}" class="flex-1 bg-gray-500 text-white py-2 rounded-lg hover:bg-gray-600 transition text-center font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection