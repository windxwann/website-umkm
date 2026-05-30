@extends('admin.layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@section('content')
<div class="max-w-6xl mx-auto">
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Settings Form (2/3 width) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info Restoran -->
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-8">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i data-lucide="store" class="w-4 h-4 text-orange-600"></i>
                        Informasi Restoran
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Nama Restoran</label>
                            <input type="text" name="restaurant_name" value="{{ old('restaurant_name', $settings['restaurant_name'] ?? '') }}" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">No. Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $settings['email'] ?? '') }}" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5 transition-all">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Alamat</label>
                            <textarea name="address" rows="2" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5 transition-all resize-none">{{ old('address', $settings['address'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Jam Operasional -->
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-8">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-orange-600"></i>
                        Jam Operasional
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Senin - Jumat</label>
                            <div class="flex gap-2">
                                <input type="time" name="mon_fri_open" value="{{ old('mon_fri_open', $settings['mon_fri_open'] ?? '10:00') }}" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5">
                                <input type="time" name="mon_fri_close" value="{{ old('mon_fri_close', $settings['mon_fri_close'] ?? '22:00') }}" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5">
                            </div>
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Sabtu - Minggu</label>
                            <div class="flex gap-2">
                                <input type="time" name="sat_sun_open" value="{{ old('sat_sun_open', $settings['sat_sun_open'] ?? '09:00') }}" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5">
                                <input type="time" name="sat_sun_close" value="{{ old('sat_sun_close', $settings['sat_sun_close'] ?? '23:00') }}" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keuangan -->
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-8">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i data-lucide="banknote" class="w-4 h-4 text-orange-600"></i>
                        Pajak & Pembayaran
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">PPN (%)</label>
                            <input type="number" name="tax" value="{{ old('tax', $settings['tax'] ?? 11) }}" step="0.1" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Service Charge (%)</label>
                            <input type="number" name="service_charge" value="{{ old('service_charge', $settings['service_charge'] ?? 5) }}" step="0.1" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold focus:ring-4 focus:ring-orange-500/5">
                        </div>
                        <!-- Metode Pembayaran -->
                        <div class="sm:col-span-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Metode Pembayaran</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <label class="flex items-center gap-2 bg-slate-50 p-3 rounded-xl cursor-pointer hover:bg-orange-50 transition">
                                    <input type="checkbox" name="payment_cashier" value="1" {{ ($settings['payment_cashier'] ?? true) ? 'checked' : '' }} class="text-orange-600 rounded">
                                    <span class="text-[10px] font-bold text-slate-700">Tunai</span>
                                </label>
                                <label class="flex items-center gap-2 bg-slate-50 p-3 rounded-xl cursor-pointer hover:bg-orange-50 transition">
                                    <input type="checkbox" name="payment_ewallet" value="1" {{ ($settings['payment_ewallet'] ?? true) ? 'checked' : '' }} class="text-orange-600 rounded">
                                    <span class="text-[10px] font-bold text-slate-700">E-Wallet</span>
                                </label>
                                <label class="flex items-center gap-2 bg-slate-50 p-3 rounded-xl cursor-pointer hover:bg-orange-50 transition">
                                    <input type="checkbox" name="payment_transfer" value="1" {{ ($settings['payment_transfer'] ?? true) ? 'checked' : '' }} class="text-orange-600 rounded">
                                    <span class="text-[10px] font-bold text-slate-700">Transfer</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Bank -->
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-8">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i data-lucide="building-2" class="w-4 h-4 text-orange-600"></i>
                        Rekening Bank
                    </h3>
                    <div class="space-y-4">
                        @foreach([1, 2] as $i)
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <input type="text" name="bank{{ $i }}_name" value="{{ old('bank'.$i.'_name', $settings['bank'.$i.'_name'] ?? '') }}" placeholder="Bank {{ $i }}" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold">
                            <input type="text" name="bank{{ $i }}_account_number" value="{{ old('bank'.$i.'_account_number', $settings['bank'.$i.'_account_number'] ?? '') }}" placeholder="No. Rek {{ $i }}" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold">
                            <input type="text" name="bank{{ $i }}_account_name" value="{{ old('bank'.$i.'_account_name', $settings['bank'.$i.'_account_name'] ?? '') }}" placeholder="Atas Nama {{ $i }}" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold">
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Banner Promosi -->
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-8">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i data-lucide="image" class="w-4 h-4 text-orange-600"></i>
                        Banner Promosi
                    </h3>
                    <div class="space-y-6">
                        @for($i = 1; $i <= 3; $i++)
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <h4 class="text-[9px] font-black text-orange-600 uppercase mb-3">Banner Slide #{{ $i }}</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <input type="text" name="banner{{ $i }}_title" value="{{ old('banner'.$i.'_title', $settings['banner'.$i.'_title'] ?? '') }}" placeholder="Judul Banner" class="w-full bg-white border-none px-4 py-2 rounded-lg text-[10px] font-bold">
                                    <textarea name="banner{{ $i }}_desc" placeholder="Deskripsi..." class="w-full bg-white border-none px-4 py-2 rounded-lg text-[10px] font-bold resize-none">{{ old('banner'.$i.'_desc', $settings['banner'.$i.'_desc'] ?? '') }}</textarea>
                                </div>
                                <div x-data="{ bPreview: '{{ isset($settings['banner'.$i.'_image']) && $settings['banner'.$i.'_image'] ? asset('storage/'.$settings['banner'.$i.'_image']) : '' }}' }">
                                    <input type="file" name="banner{{ $i }}_image" class="hidden" x-ref="bInput{{$i}}" @change="bPreview = URL.createObjectURL($event.target.files[0])">
                                    <div @click="$refs.bInput{{$i}}.click()" class="w-full h-24 bg-white rounded-xl border-2 border-dashed flex items-center justify-center cursor-pointer overflow-hidden">
                                        <template x-if="bPreview"><img :src="bPreview" class="w-full h-full object-cover"></template>
                                        <template x-if="!bPreview"><i data-lucide="image-plus" class="w-6 h-6 text-slate-300"></i></template>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Right: Branding -->
            <div class="space-y-6">
                <!-- Logo & Favicon -->
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-8">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6 px-1">Logo & Favicon</h3>
                    <div class="space-y-4">
                        <div class="relative" x-data="{ logoPreview: '{{ $settings['logo'] ? asset('storage/'.$settings['logo']) : '' }}' }">
                            <input type="file" name="logo" class="hidden" x-ref="logoInput" @change="logoPreview = URL.createObjectURL($event.target.files[0])">
                            <div @click="$refs.logoInput.click()" class="w-full aspect-video bg-slate-50 rounded-2xl border-2 border-dashed flex items-center justify-center cursor-pointer overflow-hidden">
                                <template x-if="logoPreview"><img :src="logoPreview" class="w-full h-full object-contain"></template>
                                <template x-if="!logoPreview"><i data-lucide="image-plus" class="w-8 h-8 text-slate-300"></i></template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QRIS -->
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-8" x-data="{ qrisPreview: '{{ isset($settings['qris_image']) && $settings['qris_image'] ? asset('storage/'.$settings['qris_image']) : '' }}' }">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6 px-1">Informasi QRIS</h3>
                    
                    <div class="space-y-3 mb-4">
                        <input type="text" name="qris_merchant_name" value="{{ old('qris_merchant_name', $settings['qris_merchant_name'] ?? '') }}" placeholder="Nama Merchant" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold">
                        <input type="text" name="qris_nmid" value="{{ old('qris_nmid', $settings['qris_nmid'] ?? '') }}" placeholder="NMID" class="w-full bg-slate-50 border-none px-4 py-2.5 rounded-xl text-[10px] font-bold">
                    </div>

                    <div class="relative">
                        <input type="file" name="qris_image" class="hidden" x-ref="qrisInput" @change="qrisPreview = URL.createObjectURL($event.target.files[0])">
                        <div @click="$refs.qrisInput.click()" class="w-full aspect-square bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center cursor-pointer overflow-hidden">
                            <template x-if="qrisPreview"><img :src="qrisPreview" class="w-full h-full object-contain"></template>
                            <template x-if="!qrisPreview"><i data-lucide="image-plus" class="w-8 h-8 text-slate-300"></i></template>
                        </div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-2 text-center">Klik untuk ganti QRIS</p>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full mt-8 py-4 bg-orange-600 text-white rounded-[1.5rem] text-[11px] font-black uppercase tracking-widest shadow-xl shadow-orange-600/20 hover:scale-[1.02] transition-all">
            Simpan Perubahan
        </button>
    </form>
</div>
@endsection
