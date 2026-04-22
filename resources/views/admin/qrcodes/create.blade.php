@extends('admin.layouts.app')

@section('title', 'Buat QR Code')
@section('page-title', 'Buat QR Code Baru')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="bg-orange-600 px-6 py-4 rounded-t-xl">
            <h2 class="text-white font-semibold text-lg">Buat QR Code Baru</h2>
        </div>

        <form action="{{ route('admin.qrcodes.store') }}" method="POST" class="p-6">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Nomor Meja</label>
                <input type="text" name="meja" placeholder="Contoh: 01, 02, 03"
                       class="w-full px-4 py-2 border rounded-lg focus:border-orange-500"
                       value="{{ old('meja') }}">
                <p class="text-xs text-gray-500 mt-1">Kosongkan untuk QR umum</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Nama Tempat/Lokasi</label>
                <input type="text" name="nama_tempat" placeholder="Contoh: Ruang VIP, Outdoor, Dll"
                       class="w-full px-4 py-2 border rounded-lg focus:border-orange-500"
                       value="{{ old('nama_tempat') }}">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Catatan</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500"
                          placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
            </div>

            <div class="bg-gray-50 rounded-lg p-3 mb-5">
                <div class="flex items-center gap-2">
                    <i class="fas fa-info-circle text-orange-600"></i>
                    <p class="text-sm text-gray-600">Format QR: <span class="font-mono">QR-MEJA-[tanggal]-[nomor]</span></p>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
                <a href="{{ route('admin.qrcodes.index') }}" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection