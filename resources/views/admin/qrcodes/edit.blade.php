@extends('admin.layouts.app')

@section('title', 'Edit QR Code')
@section('page-title', 'Edit QR Code')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="bg-orange-600 px-6 py-4 rounded-t-xl">
            <h2 class="text-white font-semibold text-lg">Edit QR Code</h2>
        </div>

        <form action="{{ route('admin.qrcodes.update', $qrcode) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Kode QR</label>
                <input type="text" value="{{ $qrcode->code }}" class="w-full px-4 py-2 border rounded-lg bg-gray-100" readonly>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Nomor Meja</label>
                <input type="text" name="meja" value="{{ str_replace('Meja ', '', $qrcode->meja ?? '') }}"
                       class="w-full px-4 py-2 border rounded-lg focus:border-orange-500">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Nama Tempat/Lokasi</label>
                <input type="text" name="nama_tempat" value="{{ $qrcode->nama_tempat }}"
                       class="w-full px-4 py-2 border rounded-lg focus:border-orange-500">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500">
                    <option value="active" {{ $qrcode->status === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $qrcode->status === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Catatan</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500">{{ $qrcode->notes }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700">
                    <i class="fas fa-save mr-2"></i> Update
                </button>
                <a href="{{ route('admin.qrcodes.index') }}" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection