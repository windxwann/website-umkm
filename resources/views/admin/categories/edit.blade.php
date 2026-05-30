@extends('admin.layouts.app')

@section('title', 'Edit Kategori')
@section('page-title', 'Kategori')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Breadcrumb & Header - Compact -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.categories.index') }}" 
           class="p-2 bg-white border border-slate-100 text-slate-400 hover:text-slate-600 rounded-xl transition-all shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-lg font-black text-slate-900 tracking-tight leading-none">Edit Kategori</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">#CAT-{{ str_pad($category->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Basic Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-8">
                    <div class="space-y-5">
                        <!-- Nama Kategori -->
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Nama Kategori <span class="text-rose-500">*</span></label>
                            <div class="relative group">
                                <i data-lucide="tag" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-orange-500 transition-colors"></i>
                                <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                                       placeholder="Contoh: Makanan Utama"
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                            </div>
                            @error('name') <p class="text-rose-500 text-[10px] font-bold mt-1 px-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Deskripsi Kategori</label>
                            <textarea name="description" rows="4" 
                                      placeholder="Jelaskan kategori ini..."
                                      class="w-full px-4 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-4 focus:ring-orange-500/5 transition-all resize-none">{{ old('description', $category->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Image -->
            <div class="space-y-6">
                <!-- Image Upload Card -->
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-8" x-data="{ imagePreview: '{{ $category->image ? asset('storage/' . (str_contains($category->image, 'categories/') ? $category->image : 'categories/' . $category->image)) : null }}' }">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 px-1">Gambar Kategori</h3>
                    
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
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-tight">Klik untuk Ganti</p>
                                </div>
                            </template>

                            <template x-if="imagePreview">
                                <div class="w-full h-full relative">
                                    <img :src="imagePreview" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <p class="text-white text-[10px] font-black uppercase tracking-widest">Ganti Gambar</p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    @error('image') <p class="text-rose-500 text-[10px] font-bold mt-2 px-1">{{ $message }}</p> @enderror
                </div>

                <!-- Submit Buttons -->
                <button type="submit" 
                        class="w-full py-4 bg-orange-600 text-white rounded-[1.5rem] text-[11px] font-black uppercase tracking-widest shadow-xl shadow-orange-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 group">
                    <i data-lucide="save" class="w-4 h-4 group-hover:rotate-12 transition-transform"></i>
                    Simpan Perubahan
                </button>
                
                <a href="{{ route('admin.categories.index') }}" 
                   class="w-full py-3 bg-white border border-slate-200 text-slate-400 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest hover:text-slate-600 hover:bg-slate-50 transition-all flex items-center justify-center">
                    Batal & Kembali
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
