@extends('admin.layouts.app')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

@section('content')
<div class="container mx-auto px-4 py-4 md:py-8">
    <!-- Header Mobile Friendly -->
    <div class="mb-6 md:mb-8 bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-4 md:p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-1 md:mb-2">Edit Produk</h1>
                <p class="text-orange-100 text-sm md:text-base">
                    <i class="fas fa-edit mr-2"></i>Edit informasi produk yang sudah ada
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-box-open text-6xl opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="p-4 md:p-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <!-- Nama Produk -->
                    <div class="col-span-2">
                        <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-tag"></i>
                            </span>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', $product->name) }}" 
                                   required
                                   placeholder="Contoh: Nasi Goreng Spesial"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-folder"></i>
                            </span>
                            <select name="category_id" required class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent appearance-none">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </div>
                        @error('category_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">
                            Harga <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-money-bill-wave"></i>
                            </span>
                            <input type="number" 
                                   name="price" 
                                   value="{{ old('price', $product->price) }}" 
                                   required
                                   placeholder="Contoh: 25000"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div class="col-span-2">
                        <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">
                            Deskripsi Produk
                        </label>
                        <div class="relative">
                            <textarea name="description" 
                                      rows="4" 
                                      placeholder="Masukkan deskripsi produk (opsional) - Contoh: Nasi goreng dengan bumbu spesial, telur, dan pilihan topping..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition resize-y">{{ old('description', $product->description) }}</textarea>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Deskripsi akan membantu pelanggan mengetahui detail produk
                        </p>
                    </div>

                    <!-- Gambar Saat Ini -->
                    @if($product->image)
                    <div class="col-span-2">
                        <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">
                            Gambar Saat Ini
                        </label>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            @php
                                $imagePath = $product->image;
                                if (!str_contains($imagePath, 'products/')) {
                                    $imagePath = 'products/' . $imagePath;
                                }
                            @endphp
                            <img src="{{ asset('storage/' . $imagePath) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-24 h-24 object-cover rounded-lg border-2 border-orange-500 shadow-md" 
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/150?text=No+Image';">
                            <div class="flex-1">
                                <p class="text-sm text-gray-600 mb-2">{{ basename($product->image) }}</p>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="remove_image" value="1" class="w-4 h-4 text-red-600 rounded focus:ring-red-500 mr-2">
                                    <span class="text-sm text-red-600 hover:text-red-700 transition">
                                        <i class="fas fa-trash-alt mr-1"></i>Hapus gambar
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Upload Gambar Baru -->
                    <div class="col-span-2" x-data="{ imagePreview: '', imageName: '' }">
                        <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">
                            Gambar Baru
                        </label>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <!-- Preview Area -->
                            <div class="flex-shrink-0">
                                <template x-if="!imagePreview">
                                    <div class="w-24 h-24 bg-gray-100 rounded-lg flex flex-col items-center justify-center border-2 border-dashed border-gray-300">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-1"></i>
                                        <span class="text-xs text-gray-400">Preview</span>
                                    </div>
                                </template>
                                <template x-if="imagePreview">
                                    <div class="relative">
                                        <img :src="imagePreview" 
                                             class="w-24 h-24 object-cover rounded-lg border-2 border-orange-500 shadow-md">
                                        <button type="button" 
                                                @click="imagePreview = ''; $refs.fileInput.value = ''; imageName = ''"
                                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Upload Area -->
                            <div class="flex-1">
                                <div class="relative">
                                    <input type="file" name="image" accept="image/*" x-ref="fileInput"
                                           @change="
                                               const file = $event.target.files[0];
                                               if (file) {
                                                   imagePreview = URL.createObjectURL(file);
                                                   imageName = file.name;
                                               } else {
                                                   imagePreview = '';
                                                   imageName = '';
                                               }
                                           "
                                           class="hidden">
                                    <button type="button" 
                                            @click="$refs.fileInput.click()"
                                            class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition text-gray-600 flex items-center justify-center gap-2">
                                        <i class="fas fa-upload text-orange-500"></i>
                                        <span x-text="imageName || 'Klik untuk pilih gambar baru'"></span>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-2 flex items-center gap-2">
                                    <i class="fas fa-info-circle"></i>
                                    Kosongkan jika tidak ingin mengubah gambar | Format: JPG, PNG, JPEG | Maks: 2MB
                                </p>
                            </div>
                        </div>
                        @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Tersedia -->
                    <div class="col-span-2">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <label class="flex items-center cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" name="is_available" value="1" 
                                           {{ old('is_available', $product->is_available) ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-orange-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                                </div>
                                <span class="ml-3 text-gray-700 font-medium">
                                    <i class="fas fa-check-circle text-green-600 mr-1"></i>
                                    Produk tersedia untuk dipesan
                                </span>
                            </label>
                            <p class="text-xs text-gray-500 mt-2 ml-12">
                                Nonaktifkan jika produk sedang habis atau tidak tersedia sementara
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Button Actions -->
                <div class="flex flex-col sm:flex-row gap-3 mt-8 pt-4 border-t">
                    <!-- Button Submit -->
                    <button 
                        type="submit"
                        class="bg-orange-600 text-white py-3 px-4 rounded-lg hover:bg-orange-700 transition font-semibold flex items-center justify-center shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <i class="fas fa-save mr-2"></i>
                        Update Produk
                    </button>

                    <!-- Button Cancel -->
                    <a 
                        href="{{ route('admin.products.index') }}"
                        role="button"
                        class="bg-gray-500 text-white py-3 px-4 rounded-lg hover:bg-gray-600 transition text-center font-semibold flex items-center justify-center shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-gray-400">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Informasi Tambahan -->
        <div class="mt-6 bg-blue-50 rounded-lg p-4 border border-blue-200">
            <div class="flex items-start gap-3">
                <i class="fas fa-lightbulb text-blue-500 text-xl mt-0.5"></i>
                <div>
                    <h4 class="font-semibold text-blue-800 text-sm md:text-base">Tips Mengedit Produk</h4>
                    <ul class="text-xs md:text-sm text-blue-700 mt-1 space-y-1">
                        <li><i class="fas fa-check-circle mr-1 text-xs"></i> Periksa kembali data produk sebelum menyimpan perubahan</li>
                        <li><i class="fas fa-check-circle mr-1 text-xs"></i> Update gambar jika perlu untuk tampilan yang lebih menarik</li>
                        <li><i class="fas fa-check-circle mr-1 text-xs"></i> Nonaktifkan produk sementara jika sedang habis, jangan dihapus</li>
                        <li><i class="fas fa-check-circle mr-1 text-xs"></i> Perubahan akan langsung tampil di halaman menu</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Toggle Switch Animation */
    .peer:checked ~ .peer-checked\:bg-orange-600 {
        background-color: #f97316;
    }
</style>
@endpush
@endsection