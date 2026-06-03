@extends('admin.layouts.app')

@section('title', 'Edit Produk')
@section('page-title', 'Produk')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb & Header - Compact -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.products.index') }}" 
           class="p-2 bg-white border border-slate-100 text-slate-400 hover:text-slate-600 rounded-xl transition-all shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-lg font-black text-slate-900 tracking-tight leading-none">Edit Produk</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">#PROD-{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Basic Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-8">
                    <div class="space-y-5">
                        <!-- Nama Produk -->
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Nama Produk <span class="text-rose-500">*</span></label>
                            <div class="relative group">
                                <i data-lucide="tag" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-orange-500 transition-colors"></i>
                                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                       placeholder="Contoh: Nasi Goreng Spesial"
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                            </div>
                            @error('name') <p class="text-rose-500 text-[10px] font-bold mt-1 px-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Kategori -->
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Kategori <span class="text-rose-500">*</span></label>
                                <div class="relative group">
                                    <i data-lucide="grid" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-orange-500 transition-colors"></i>
                                    <select name="category_id" required class="w-full pl-11 pr-10 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all appearance-none cursor-pointer">
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"></i>
                                </div>
                            </div>

                            <!-- Harga -->
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Harga (Rp) <span class="text-rose-500">*</span></label>
                                <div class="relative group">
                                    <i data-lucide="banknote" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-orange-500 transition-colors"></i>
                                    <input type="number" name="price" value="{{ old('price', $product->price) }}" required
                                           placeholder="25000"
                                           class="w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                                </div>
                                @error('price') <p class="text-rose-500 text-[10px] font-bold mt-1 px-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Stok -->
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Stok (Opsional)</label>
                            <div class="relative group">
                                <i data-lucide="boxes" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-orange-500 transition-colors"></i>
                                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0"
                                       placeholder="Kosongkan jika stok tidak dibatasi"
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                            </div>
                            @error('stock') <p class="text-rose-500 text-[10px] font-bold mt-1 px-1">{{ $message }}</p> @enderror
                            <p class="text-[9px] text-slate-400 font-medium px-1 mt-1">Isi angka untuk mode otomatis. Kosongkan untuk mode semi-manual (tidak terbatas).</p>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Deskripsi Produk</label>
                            <textarea name="description" rows="4" 
                                      placeholder="Jelaskan detail menu ini kepada pelanggan..."
                                      class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all resize-none">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Media & Status -->
            <div class="space-y-6">
                <!-- Image Upload Card -->
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-8" x-data="{ imagePreview: '{{ $product->image ? asset('storage/' . (str_contains($product->image, 'products/') ? $product->image : 'products/' . $product->image)) : null }}' }">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 px-1">Foto Menu</h3>
                    
                    <div class="relative group">
                        <input type="file" name="image" accept="image/*" class="hidden" x-ref="fileInput"
                               @change="const file = $event.target.files[0]; if (file) imagePreview = URL.createObjectURL(file);">
                        
                        <div @click="$refs.fileInput.click()" 
                             class="aspect-square bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 group-hover:border-orange-500 group-hover:bg-orange-50/30 transition-all cursor-pointer overflow-hidden flex flex-col items-center justify-center gap-3">
                            
                            <template x-if="!imagePreview">
                                <div class="flex flex-col items-center justify-center text-center px-4">
                                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-slate-300 shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                        <i data-lucide="image-plus" class="w-6 h-6"></i>
                                    </div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-tight">Klik untuk Upload</p>
                                    <p class="text-[8px] font-bold text-slate-300 uppercase mt-1">PNG, JPG up to 2MB</p>
                                </div>
                            </template>

                            <template x-if="imagePreview">
                                <div class="w-full h-full relative">
                                    <img :src="imagePreview" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/300?text=Error+Loading+Image'">
                                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <p class="text-white text-[10px] font-black uppercase tracking-widest">Ganti Gambar</p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    @error('image') <p class="text-rose-500 text-[10px] font-bold mt-2 px-1">{{ $message }}</p> @enderror
                </div>

                <!-- Availability Card -->
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-8">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 px-1">Pengaturan</h3>
                    
                    <label class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl cursor-pointer group hover:bg-orange-50/50 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-white rounded-xl flex items-center justify-center text-emerald-500 shadow-sm">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-900 uppercase">Tersedia</p>
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter mt-0.5">Dapat dipesan pembeli</p>
                            </div>
                        </div>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_available" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-orange-600 shadow-inner"></div>
                        </div>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button type="submit" 
                            class="w-full py-4 bg-orange-600 text-white rounded-[1.5rem] text-[11px] font-black uppercase tracking-widest shadow-xl shadow-orange-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 group">
                        <i data-lucide="save" class="w-4 h-4 group-hover:rotate-12 transition-transform"></i>
                        Simpan Perubahan
                    </button>
                    
                    <a href="{{ route('admin.products.index') }}" 
                       class="w-full py-3 bg-white border border-slate-200 text-slate-400 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest hover:text-slate-600 hover:bg-slate-50 transition-all flex items-center justify-center">
                        Batal & Kembali
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
