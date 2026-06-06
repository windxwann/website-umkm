@extends('layouts.app')

@section('title', 'Tentang Kami')

@section('full_width_content')

{{-- 1. Hero Section --}}
<section class="relative h-[80vh] min-h-[600px] flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0 bg-slate-900">
        <!-- Elegant dark food image for background -->
        <img src="https://images.unsplash.com/photo-1544025162-d76694265947?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" class="w-full h-full object-cover object-center opacity-70" alt="Hero">
        <!-- Gradient overlay transitioning to white at the bottom -->
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/40 via-slate-900/20 to-white"></div>
    </div>
    
    <!-- Content -->
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto -mt-20">
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-white mb-6 drop-shadow-xl tracking-tight leading-tight">
            Cita Rasa dalam Piring,<br>Kenangan di Tiap Suapan
        </h1>
        <div class="flex items-center justify-center gap-4 mt-8 opacity-80">
            <span class="w-12 h-1 rounded-full bg-white"></span>
            <span class="w-3 h-3 rounded-full bg-orange-500 shadow-lg shadow-orange-500/50"></span>
            <span class="w-12 h-1 rounded-full bg-white"></span>
        </div>
    </div>
</section>

{{-- 2. Our Philosophy --}}
<section class="container mx-auto px-4 py-20 lg:py-32 bg-white">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-24 items-center max-w-6xl mx-auto">
        <!-- Image -->
        <div class="relative order-2 md:order-1">
            <div class="aspect-[4/5] overflow-hidden relative z-10 w-[90%] bg-slate-50 rounded-[2rem] shadow-2xl border border-slate-100">
                <img src="https://images.unsplash.com/photo-1577219491135-ce391730fb2c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Chef preparing food" class="w-full h-full object-cover">
            </div>
            <div class="absolute top-10 right-0 w-[85%] h-full bg-orange-50 rounded-[2rem] -z-0 shadow-inner"></div>
        </div>
        
        <!-- Text -->
        <div class="order-1 md:order-2">
            <div class="inline-flex items-center gap-3 bg-orange-50 px-4 py-2 rounded-xl mb-6">
                <i class="fas fa-heart text-orange-600"></i>
                <span class="text-orange-600 font-black text-xs tracking-widest uppercase">Filosofi Kami</span>
            </div>
            <div class="text-slate-600 space-y-6 leading-relaxed text-sm md:text-base">
                <p>
                    <strong class="text-slate-900 font-black">Dapoer Cemal Cemil Jiemas</strong> hadir dari kecintaan terhadap cita rasa khas Nusantara dan semangat untuk menghadirkan pengalaman kuliner yang hangat bagi setiap tamu. Berawal dari usaha sederhana di Kota Banjar, kami tumbuh dengan satu komitmen: menyajikan hidangan berkualitas dengan rasa yang autentik dan pelayanan yang tulus.
                </p>
                <div class="border-l-4 border-orange-500 pl-6 py-3 my-8 bg-slate-50 rounded-r-2xl text-slate-800 text-lg font-medium italic shadow-sm">
                    "Setiap hidangan yang kami sajikan bukan sekadar makanan, melainkan cerita, kehangatan, dan kebahagiaan yang ingin kami bagikan."
                </div>
                <p>
                    Menggabungkan resep pilihan, bahan-bahan segar, dan inovasi pelayanan modern, kami terus berupaya menjadi destinasi kuliner yang memberikan kenyamanan, kualitas, dan kenangan terbaik dalam setiap kunjungan.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- 3. Signature Creations --}}
<section class="py-24 lg:py-32 bg-slate-50 rounded-[3rem] mx-2 md:mx-4 shadow-sm border border-slate-100 mb-20">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="text-center mb-16 lg:mb-20">
            <div class="inline-flex items-center gap-3 bg-white px-4 py-2 rounded-xl mb-6 shadow-sm border border-slate-100">
                <i class="fas fa-star text-orange-600"></i>
                <span class="text-orange-600 font-black text-xs tracking-widest uppercase">Spesialitas</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight">Menu Andalan</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-10">
            <!-- Item 1 -->
            <div class="group bg-white p-4 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="overflow-hidden mb-6 aspect-square bg-slate-50 rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1580476262798-bddd9f4b7369?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Ikan Bakar" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-700 ease-out">
                </div>
                <div class="px-2 text-center pb-2">
                    <h3 class="text-xl font-black text-slate-900 mb-2">Ikan Bakar Segar</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Ikan pilihan terbaik yang dibakar dengan bumbu rempah khas nusantara, disajikan dengan sambal terasi istimewa.</p>
                </div>
            </div>
            <!-- Item 2 -->
            <div class="group bg-white p-4 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="overflow-hidden mb-6 aspect-square bg-slate-50 rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Ayam Goreng" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-700 ease-out">
                </div>
                <div class="px-2 text-center pb-2">
                    <h3 class="text-xl font-black text-slate-900 mb-2">Ayam Goreng Rempah</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Ayam goreng gurih dengan bumbu marinasi rahasia, digoreng hingga renyah keemasan.</p>
                </div>
            </div>
            <!-- Item 3 -->
            <div class="group bg-white p-4 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="overflow-hidden mb-6 aspect-square bg-slate-50 rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1534080564583-6be75777b70a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Seafood" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-700 ease-out">
                </div>
                <div class="px-2 text-center pb-2">
                    <h3 class="text-xl font-black text-slate-900 mb-2">Aneka Seafood Sunda</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Sajian laut segar yang diolah dengan cita rasa Sunda otentik yang menggugah selera.</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-12">
            <a href="{{ route('menu') }}" class="inline-flex items-center px-8 py-4 bg-slate-900 text-white font-black uppercase tracking-widest text-sm rounded-xl hover:bg-orange-600 transition-colors duration-300 shadow-lg hover:shadow-orange-600/30 hover:-translate-y-1">
                Lihat Menu Lengkap
            </a>
        </div>
    </div>
</section>

{{-- 4. Gallery Grid --}}
<section class="py-12 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 relative z-0">
            <!-- Big Image (Left) -->
            <div class="h-[300px] md:h-[600px] w-full relative group overflow-hidden bg-slate-100 rounded-[2rem] shadow-lg">
                <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Interior" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700">
            </div>
            <!-- Two Smaller Images (Right) -->
            <div class="grid grid-rows-2 gap-4 md:gap-6 h-[400px] md:h-[600px] w-full">
                <div class="relative group overflow-hidden h-full w-full bg-slate-100 rounded-[2rem] shadow-lg">
                    <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Food Detail" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700">
                </div>
                <div class="relative group overflow-hidden h-full w-full bg-slate-100 rounded-[2rem] shadow-lg">
                    <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Ingredients" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700">
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 5. Find Us --}}
<section class="py-24 lg:py-32 bg-white">
    <div class="container mx-auto px-4 max-w-5xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 items-stretch shadow-2xl rounded-[2rem] overflow-hidden bg-white border border-slate-100">
            <!-- Info -->
            <div class="p-10 lg:p-16 flex flex-col justify-center bg-slate-50 relative">
                <!-- Decorative element -->
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-white rounded-full opacity-50 blur-xl"></div>
                
                <div class="inline-flex items-center gap-3 bg-white px-4 py-2 rounded-xl mb-6 shadow-sm border border-slate-100 self-start">
                    <i class="fas fa-map-marker-alt text-orange-600"></i>
                    <span class="text-orange-600 font-black text-xs tracking-widest uppercase">Temukan Kami</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 mb-10">Kunjungi Resto Kami</h2>
                
                <div class="space-y-8 relative z-10">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-slate-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-map-pin text-orange-500 text-sm"></i>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Alamat</h4>
                            <p class="text-slate-900 font-medium text-sm leading-relaxed">{{ setting('address', 'Jl. Kuliner No. 123, Jakarta') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-slate-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-clock text-orange-500 text-sm"></i>
                        </div>
                        <div class="w-full">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Jam Operasional</h4>
                            <ul class="text-slate-900 font-medium text-sm space-y-2 mt-2">
                                <li class="flex justify-between border-b border-slate-200 pb-2">
                                    <span>Senin - Jumat</span>
                                    <span>{{ setting('mon_fri_open', '10:00') }} - {{ setting('mon_fri_close', '22:00') }}</span>
                                </li>
                                <li class="flex justify-between pt-1">
                                    <span>Sabtu - Minggu</span>
                                    <span>{{ setting('sat_sun_open', '09:00') }} - {{ setting('sat_sun_close', '23:00') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-slate-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-phone text-orange-500 text-sm"></i>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kontak</h4>
                            <p class="text-slate-900 font-medium text-sm">{{ setting('phone', '0812-3456-7890') }}</p>
                            <p class="text-slate-900 font-medium text-sm">{{ setting('email', 'info@dapoercemalcemil.com') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Map -->
            <div class="h-80 lg:h-auto min-h-[400px] w-full bg-slate-200 relative">
                <!-- Using a standard map style to match the friendly/bright UI -->
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3426.1583036451357!2d108.5392796!3d-7.373341999999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6f62119bc56749%3A0xc06856d490eb2ad7!2sDapoer%20Cemal%20Cemil%20Jiemas!5e1!3m2!1sid!2sid!4v1780497999517!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="absolute inset-0 w-full h-full border-0"></iframe>
            </div>
        </div>
    </div>
</section>

@endsection
