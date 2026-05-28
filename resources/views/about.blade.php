@extends('layouts.app')

@section('title', 'Tentang Kami')

@section('content')

{{-- ====================================================
     HERO SECTION
     ==================================================== --}}
<div class="relative -mx-4 -mt-8 mb-12 overflow-hidden rounded-b-3xl"
     style="background: linear-gradient(135deg, #ea580c 0%, #f97316 40%, #fb923c 100%);">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-64 h-64 rounded-full bg-white -translate-x-32 -translate-y-32"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 rounded-full bg-white translate-x-32 translate-y-32"></div>
        <div class="absolute top-1/2 left-1/2 w-48 h-48 rounded-full bg-white -translate-x-24 -translate-y-24"></div>
    </div>
    <div class="relative container mx-auto px-4 py-16 text-center text-white">
        <div class="inline-flex items-center bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 mb-4 text-sm font-medium">
            <i class="fas fa-heart mr-2 text-red-200"></i>
            Dengan Cinta, Untuk Keluarga
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4 leading-tight drop-shadow-lg">
            Tentang Kami
        </h1>
        <p class="text-lg md:text-xl text-orange-100 max-w-2xl mx-auto leading-relaxed">
            Dapoer Cemal Cemil Jiemas — Menyajikan cita rasa otentik nusantara dengan penuh kehangatan dan ketulusan sejak pertama kali kami berdiri.
        </p>
        <div class="mt-8 flex flex-wrap justify-center gap-6">
            <div class="text-center">
                <p class="text-4xl font-extrabold">5+</p>
                <p class="text-orange-100 text-sm mt-1">Tahun Melayani</p>
            </div>
            <div class="w-px bg-white/30 hidden sm:block"></div>
            <div class="text-center">
                <p class="text-4xl font-extrabold">50+</p>
                <p class="text-orange-100 text-sm mt-1">Menu Pilihan</p>
            </div>
            <div class="w-px bg-white/30 hidden sm:block"></div>
            <div class="text-center">
                <p class="text-4xl font-extrabold">1000+</p>
                <p class="text-orange-100 text-sm mt-1">Pelanggan Puas</p>
            </div>
        </div>
    </div>
</div>

{{-- ====================================================
     CERITA KAMI
     ==================================================== --}}
<section class="mb-16" id="cerita-kami">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
        <div>
            <div class="inline-flex items-center text-orange-600 font-semibold text-sm mb-3 tracking-wider uppercase">
                <span class="w-8 h-0.5 bg-orange-500 mr-3"></span>
                Cerita Kami
            </div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-5 leading-tight">
                Berawal dari Dapur Rumah,<br class="hidden md:block"> Kini Menjadi Tempat Favorit
            </h2>
            <div class="space-y-4 text-gray-600 leading-relaxed">
                <p>
                    <strong class="text-orange-600">Dapoer Cemal Cemil Jiemas</strong> lahir dari kecintaan mendalam terhadap masakan rumahan Indonesia. Bermula dari dapur kecil yang hangat, kami meracik setiap hidangan dengan bahan-bahan segar pilihan dan bumbu rempah autentik yang telah diwariskan turun-temurun.
                </p>
                <p>
                    Nama "Cemal Cemil" mencerminkan semangat kami — bahwa makan bukan sekadar kebutuhan, tetapi sebuah pengalaman penuh kebahagiaan. Kami percaya bahwa setiap gigitan harus membawa senyum, seperti saat menikmati masakan buatan ibu di rumah.
                </p>
                <p>
                    Dengan dukungan teknologi pemesanan modern melalui QR Code, kami terus berinovasi agar pengalaman makan Anda semakin mudah, cepat, dan menyenangkan — tanpa mengorbankan kehangatan layanan personal yang selalu menjadi ciri khas kami.
                </p>
            </div>
        </div>
        <div class="relative">
            <div class="aspect-w-4 aspect-h-3 rounded-3xl overflow-hidden shadow-2xl">
                <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&q=80"
                     alt="Dapur Dapoer Cemal Cemil Jiemas"
                     class="w-full h-72 lg:h-96 object-cover">
            </div>
            <div class="absolute -bottom-5 -left-5 bg-orange-600 text-white rounded-2xl shadow-xl px-6 py-4">
                <p class="text-3xl font-extrabold">5+</p>
                <p class="text-sm text-orange-100">Tahun Pengalaman</p>
            </div>
            <div class="absolute -top-5 -right-5 bg-white rounded-2xl shadow-xl px-6 py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-star text-yellow-400 text-xl"></i>
                    <div>
                        <p class="text-2xl font-extrabold text-gray-800">4.9</p>
                        <p class="text-xs text-gray-500">Rating Pelanggan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ====================================================
     NILAI KAMI
     ==================================================== --}}
<section class="mb-16" id="nilai-kami">
    <div class="text-center mb-10">
        <div class="inline-flex items-center text-orange-600 font-semibold text-sm mb-3 tracking-wider uppercase">
            <span class="w-8 h-0.5 bg-orange-500 mr-3"></span>
            Nilai Kami
            <span class="w-8 h-0.5 bg-orange-500 ml-3"></span>
        </div>
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800">Mengapa Memilih Kami?</h2>
        <p class="text-gray-500 mt-3 max-w-xl mx-auto">Kami berkomitmen memberikan yang terbaik dalam setiap aspek pelayanan.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
        $values = [
            ['icon' => 'fas fa-leaf', 'color' => 'green', 'title' => 'Bahan Segar', 'desc' => 'Setiap bahan dipilih langsung dari sumber terpercaya setiap hari untuk menjamin kesegaran dan kualitas.'],
            ['icon' => 'fas fa-fire-alt', 'color' => 'orange', 'title' => 'Resep Autentik', 'desc' => 'Bumbu dan racikan kami menggunakan resep turun-temurun yang telah teruji dan dicintai banyak orang.'],
            ['icon' => 'fas fa-heart', 'color' => 'red', 'title' => 'Dibuat dengan Cinta', 'desc' => 'Setiap masakan diracik dengan ketulusan dan kasih sayang, seperti masakan rumah yang sesungguhnya.'],
            ['icon' => 'fas fa-bolt', 'color' => 'yellow', 'title' => 'Layanan Cepat', 'desc' => 'Pesanan via QR Code kami pastikan diproses dengan cepat sehingga Anda tidak perlu lama menunggu.'],
        ];
        @endphp
        @foreach($values as $value)
        <div class="bg-white rounded-2xl shadow-lg p-6 text-center hover:-translate-y-1 hover:shadow-xl transition-all duration-300 group">
            <div class="w-16 h-16 bg-{{ $value['color'] }}-100 rounded-2xl flex items-center justify-center mx-auto mb-5 group-hover:scale-110 transition-transform duration-300">
                <i class="{{ $value['icon'] }} text-{{ $value['color'] }}-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-3">{{ $value['title'] }}</h3>
            <p class="text-gray-500 text-sm leading-relaxed">{{ $value['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ====================================================
     MENU UNGGULAN
     ==================================================== --}}
<section class="mb-16" id="menu-unggulan">
    <div class="bg-gradient-to-r from-orange-600 to-orange-500 rounded-3xl overflow-hidden shadow-2xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 items-center">
            <div class="p-8 md:p-12 text-white">
                <div class="inline-flex items-center bg-white/20 rounded-full px-4 py-1.5 text-sm font-medium mb-5">
                    <i class="fas fa-crown mr-2 text-yellow-300"></i>
                    Menu Andalan Kami
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4 leading-tight">
                    Seafood Segar &amp;<br>Masakan Sunda Otentik
                </h2>
                <p class="text-orange-100 mb-6 leading-relaxed">
                    Dari ikan bakar dengan sambal terasi segar, aneka tumisan sayur, hingga gorengan renyah — semua tersedia untuk memanjakan selera Anda dan keluarga.
                </p>
                <div class="space-y-3 mb-8">
                    @foreach(['Ikan Bakar / Goreng Segar', 'Aneka Tumisan Sunda', 'Gorengan Khas Jiemas', 'Minuman Segar Tradisional'] as $menu)
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                        <span class="text-white/90">{{ $menu }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('menu') }}"
                   class="inline-flex items-center bg-white text-orange-600 px-6 py-3 rounded-xl font-bold hover:bg-orange-50 transition-all shadow-lg hover:shadow-xl">
                    <i class="fas fa-utensils mr-2"></i>
                    Lihat Semua Menu
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div class="relative h-64 lg:h-auto">
                <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=80"
                     alt="Menu Unggulan Dapoer Cemal Cemil"
                     class="w-full h-full object-cover lg:rounded-none rounded-none">
                <div class="absolute inset-0 bg-gradient-to-r from-orange-600/50 to-transparent lg:flex hidden"></div>
            </div>
        </div>
    </div>
</section>

{{-- ====================================================
     JAM OPERASIONAL & KONTAK
     ==================================================== --}}
<section class="mb-16" id="kontak">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- Jam Operasional --}}
        <div class="bg-white rounded-2xl shadow-lg p-8 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
                <h3 class="text-2xl font-extrabold text-gray-800">Jam Buka</h3>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-calendar-day text-orange-400 text-sm"></i>
                        <span class="text-gray-700 font-medium">Senin – Jumat</span>
                    </div>
                    <span class="font-bold text-gray-800 bg-orange-50 px-3 py-1 rounded-full text-sm">
                        {{ setting('mon_fri_open', '10:00') }} – {{ setting('mon_fri_close', '22:00') }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-calendar-weekend text-orange-400 text-sm"></i>
                        <span class="text-gray-700 font-medium">Sabtu – Minggu</span>
                    </div>
                    <span class="font-bold text-gray-800 bg-orange-50 px-3 py-1 rounded-full text-sm">
                        {{ setting('sat_sun_open', '09:00') }} – {{ setting('sat_sun_close', '23:00') }}
                    </span>
                </div>
                <div class="flex items-center space-x-3 pt-2">
                    <div class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-green-600 font-semibold text-sm">Sedang Buka Sekarang</span>
                </div>
            </div>
        </div>

        {{-- Informasi Kontak --}}
        <div class="bg-white rounded-2xl shadow-lg p-8 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-map-marker-alt text-orange-600 text-xl"></i>
                </div>
                <h3 class="text-2xl font-extrabold text-gray-800">Temukan Kami</h3>
            </div>
            <div class="space-y-4">
                <div class="flex items-start space-x-4">
                    <div class="w-9 h-9 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-map-pin text-orange-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Alamat</p>
                        <p class="text-gray-700 font-medium leading-relaxed">{{ setting('address', 'Jl. Kuliner No. 123, Jakarta') }}</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="w-9 h-9 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-phone text-orange-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Telepon</p>
                        <a href="tel:{{ setting('phone') }}" class="text-gray-700 font-medium hover:text-orange-600 transition">{{ setting('phone', '0812-3456-7890') }}</a>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="w-9 h-9 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-envelope text-orange-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Email</p>
                        <a href="mailto:{{ setting('email') }}" class="text-gray-700 font-medium hover:text-orange-600 transition">{{ setting('email', 'info@dapoercemalcemil.com') }}</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ====================================================
     CTA SECTION
     ==================================================== --}}
<section class="mb-4">
    <div class="bg-gray-800 rounded-3xl shadow-2xl p-10 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-0 left-1/4 w-64 h-64 rounded-full bg-orange-400 -translate-y-32"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 rounded-full bg-orange-500 translate-y-40"></div>
        </div>
        <div class="relative">
            <i class="fas fa-utensils text-orange-400 text-4xl mb-5"></i>
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">
                Siap Menikmati Kelezatan Kami?
            </h2>
            <p class="text-gray-300 mb-8 max-w-lg mx-auto text-lg">
                Scan QR Code di meja Anda dan pesan langsung hidangan favorit pilihan keluarga!
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('menu') }}"
                   class="inline-flex items-center bg-orange-600 text-white px-8 py-3.5 rounded-xl font-bold hover:bg-orange-700 transition-all shadow-lg hover:shadow-orange-500/30 hover:-translate-y-0.5">
                    <i class="fas fa-utensils mr-2"></i>
                    Lihat Menu Sekarang
                </a>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center bg-white/10 text-white px-8 py-3.5 rounded-xl font-bold hover:bg-white/20 transition-all border border-white/20">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    section {
        animation: fadeInUp 0.6s ease-out both;
    }
    section:nth-child(2) { animation-delay: 0.1s; }
    section:nth-child(3) { animation-delay: 0.2s; }
    section:nth-child(4) { animation-delay: 0.3s; }
    section:nth-child(5) { animation-delay: 0.4s; }
</style>
@endpush

@endsection
