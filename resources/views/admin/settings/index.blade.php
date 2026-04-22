@extends('admin.layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan Restoran')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-gray-800 to-gray-700 text-white">
            <h2 class="text-2xl font-bold">
                <i class="fas fa-cog mr-2"></i>Pengaturan Umum
            </h2>
        </div>

        @if($errors->any())
        <div class="p-4 bg-red-50 border-b border-red-100">
            <div class="flex items-center text-red-700 mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span class="font-bold">Gagal menyimpan! Periksa input berikut:</span>
            </div>
            <ul class="list-disc list-inside text-sm text-red-600">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- Informasi Restoran -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Restoran</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Nama Restoran</label>
                            <input type="text" name="restaurant_name" value="{{ old('restaurant_name', $settings['restaurant_name'] ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">No. Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Alamat</label>
                            <textarea name="address" rows="3" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">{{ old('address', $settings['address'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $settings['email'] ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Website</label>
                            <input type="text" name="website" value="{{ old('website', $settings['website'] ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                </div>

                <!-- Jam Operasional -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Jam Operasional</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Senin - Jumat</label>
                            <div class="flex space-x-2">
                                <input type="time" name="mon_fri_open" value="{{ old('mon_fri_open', $settings['mon_fri_open'] ?? '10:00') }}"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                                <span class="self-center">-</span>
                                <input type="time" name="mon_fri_close" value="{{ old('mon_fri_close', $settings['mon_fri_close'] ?? '22:00') }}"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Sabtu - Minggu</label>
                            <div class="flex space-x-2">
                                <input type="time" name="sat_sun_open" value="{{ old('sat_sun_open', $settings['sat_sun_open'] ?? '09:00') }}"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                                <span class="self-center">-</span>
                                <input type="time" name="sat_sun_close" value="{{ old('sat_sun_close', $settings['sat_sun_close'] ?? '23:00') }}"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pajak & Biaya -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pajak & Biaya</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">PPN (%)</label>
                            <input type="number" name="tax" value="{{ old('tax', $settings['tax'] ?? 11) }}" step="0.1"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Service Charge (%)</label>
                            <input type="number" name="service_charge" value="{{ old('service_charge', $settings['service_charge'] ?? 5) }}" step="0.1"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>
                        <!-- Removed packaging fee -->
                    </div>
                </div>

                <!-- Rekening Bank -->
                <div class="pt-6 border-t border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Rekening Bank</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Nama Bank</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $settings['bank_name'] ?? '') }}" placeholder="Contoh: BCA / Mandiri"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Nomor Rekening</label>
                            <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $settings['bank_account_number'] ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Atas Nama</label>
                            <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $settings['bank_account_name'] ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                </div>

                <!-- Rekening Bank 2 -->
                <div class="pt-6 border-t border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Rekening Bank 2 (Opsional)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Nama Bank 2</label>
                            <input type="text" name="bank2_name" value="{{ old('bank2_name', $settings['bank2_name'] ?? '') }}" placeholder="Contoh: Mandiri / OVO"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Nomor Rekening 2</label>
                            <input type="text" name="bank2_account_number" value="{{ old('bank2_account_number', $settings['bank2_account_number'] ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Atas Nama 2</label>
                            <input type="text" name="bank2_account_name" value="{{ old('bank2_account_name', $settings['bank2_account_name'] ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                </div>

                <!-- QRIS -->
                <div class="pt-6 border-t border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi QRIS</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Nama Merchant</label>
                                <input type="text" name="qris_merchant_name" value="{{ old('qris_merchant_name', $settings['qris_merchant_name'] ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">NMID</label>
                                <input type="text" name="qris_nmid" value="{{ old('qris_nmid', $settings['qris_nmid'] ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            </div>
                        </div>
                        <div x-data="{ qrisPreview: '{{ isset($settings['qris_image']) && $settings['qris_image'] ? asset('storage/'.$settings['qris_image']) : '' }}' }">
                            <label class="block text-gray-700 font-semibold mb-2">Gambar QRIS</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div x-show="!qrisPreview" 
                                         class="w-32 h-32 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                                        <i class="fas fa-qrcode text-4xl text-gray-400"></i>
                                    </div>
                                    <template x-if="qrisPreview">
                                        <img :src="qrisPreview" 
                                             class="w-32 h-32 object-contain rounded-lg border-2 border-orange-500 shadow-sm">
                                    </template>
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="qris_image" accept="image/*"
                                           @change="qrisPreview = URL.createObjectURL($event.target.files[0])"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                                    <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG. Maksimal 2MB.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logo & Favicon -->
                <div class="pt-6 border-t border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Logo & Favicon</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div x-data="{ logoPreview: '{{ $settings['logo'] ? asset('storage/'.$settings['logo']) : '' }}' }">
                            <label class="block text-gray-700 font-semibold mb-2">Logo</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div x-show="!logoPreview" 
                                         class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                                        <i class="fas fa-image text-3xl text-gray-400"></i>
                                    </div>
                                    <template x-if="logoPreview">
                                        <img :src="logoPreview" 
                                             class="w-20 h-20 object-contain rounded-lg border-2 border-orange-500">
                                    </template>
                                </div>
                                <input type="file" name="logo" accept="image/*"
                                       @change="logoPreview = URL.createObjectURL($event.target.files[0])"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg text-sm">
                            </div>
                        </div>
                        <div x-data="{ faviconPreview: '{{ $settings['favicon'] ? asset('storage/'.$settings['favicon']) : '' }}' }">
                            <label class="block text-gray-700 font-semibold mb-2">Favicon</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div x-show="!faviconPreview" 
                                         class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                                        <i class="fas fa-image text-2xl text-gray-400"></i>
                                    </div>
                                    <template x-if="faviconPreview">
                                        <img :src="faviconPreview" 
                                             class="w-12 h-12 object-contain rounded-lg border-2 border-orange-500">
                                    </template>
                                </div>
                                <input type="file" name="favicon" accept="image/*"
                                       @change="faviconPreview = URL.createObjectURL($event.target.files[0])"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Metode Pembayaran</h3>
                    <div class="space-y-3">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="payment_cashier" value="1" {{ ($settings['payment_cashier'] ?? true) ? 'checked' : '' }}
                                   class="w-5 h-5 text-orange-600 rounded focus:ring-orange-500">
                            <span class="text-gray-700">Bayar di Kasir (Tunai)</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="payment_ewallet" value="1" {{ ($settings['payment_ewallet'] ?? true) ? 'checked' : '' }}
                                   class="w-5 h-5 text-orange-600 rounded focus:ring-orange-500">
                            <span class="text-gray-700">E-Wallet (QRIS)</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="payment_transfer" value="1" {{ ($settings['payment_transfer'] ?? true) ? 'checked' : '' }}
                                   class="w-5 h-5 text-orange-600 rounded focus:ring-orange-500">
                            <span class="text-gray-700">Transfer Bank</span>
                        </label>
                    </div>
                </div>

                <!-- Removed Geolocation Settings -->
            </div>
            </div>

            <!-- Banner Promosi -->
            <div class="pt-6 border-t border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Manajemen Banner Promosi (Carousel)</h3>
                
                <div class="grid grid-cols-1 gap-12">
                    @for($i = 1; $i <= 3; $i++)
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                        <h4 class="font-bold text-orange-600 mb-4">Slide Banner #{{ $i }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Banner</label>
                                    <input type="text" name="banner{{ $i }}_title" value="{{ old('banner'.$i.'_title', $settings['banner'.$i.'_title'] ?? '') }}"
                                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Singkat</label>
                                    <textarea name="banner{{ $i }}_desc" rows="2"
                                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">{{ old('banner'.$i.'_desc', $settings['banner'.$i.'_desc'] ?? '') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Link Tujuan (Opsional)</label>
                                    <input type="text" name="banner{{ $i }}_link" value="{{ old('banner'.$i.'_link', $settings['banner'.$i.'_link'] ?? '') }}"
                                           placeholder="Contoh: /menu atau #testimoni"
                                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">
                                </div>
                            </div>
                            <div x-data="{ preview: '{{ isset($settings['banner'.$i.'_image']) && $settings['banner'.$i.'_image'] ? asset('storage/'.$settings['banner'.$i.'_image']) : '' }}' }">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar Banner</label>
                                <div class="relative group h-48 w-full bg-gray-200 rounded-xl overflow-hidden border-2 border-dashed border-gray-300 flex items-center justify-center">
                                    <template x-if="preview">
                                        <img :src="preview" class="absolute inset-0 w-full h-full object-cover">
                                    </template>
                                    <div x-show="!preview" class="text-center">
                                        <i class="fas fa-image text-3xl text-gray-400 mb-2"></i>
                                        <p class="text-xs text-gray-400">Pilih gambar (1200x500px)</p>
                                    </div>
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                        <label class="bg-white text-gray-800 px-4 py-2 rounded-lg cursor-pointer text-sm font-bold shadow-lg">
                                            Ubah Gambar
                                            <input type="file" name="banner{{ $i }}_image" class="hidden" 
                                                   @change="preview = URL.createObjectURL($event.target.files[0])">
                                        </label>
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-2">Disarankan aspek rasio lebar (misal: 1200x500px) untuk hasil terbaik.</p>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-8">
                <button type="submit" class="bg-orange-600 text-white px-8 py-3 rounded-lg hover:bg-orange-700 transition font-semibold">
                    <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function getCurrentLocation() {
    if (!navigator.geolocation) {
        Swal.fire('Error', 'Browser Anda tidak mendukung Geolocation', 'error');
        return;
    }

    Swal.fire({
        title: 'Mendapatkan Lokasi...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    navigator.geolocation.getCurrentPosition(
        (position) => {
            document.getElementById('restaurant_latitude').value = position.coords.latitude;
            document.getElementById('restaurant_longitude').value = position.coords.longitude;
            Swal.fire({
                icon: 'success',
                title: 'Lokasi Berhasil Diambil!',
                text: 'Latitude dan Longitude telah diperbarui.',
                timer: 2000,
                showConfirmButton: false
            });
        },
        (error) => {
            console.error('Error getting location:', error);
            let message = 'Gagal mendapatkan lokasi.';
            if (error.code === 1) message = 'Akses lokasi ditolak oleh browser.';
            Swal.fire('Gagal', message, 'error');
        },
        { enableHighAccuracy: true }
    );
}
</script>
@endpush
@endsection