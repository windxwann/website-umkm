<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Models\Order;
use Illuminate\Http\Request;
use chillerlan\QRCode\QRCode as QRGenerator;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller
{
    // 🔥 HAPUS method showScan(), validateQr(), processScan()
    // Method tersebut HARUS ADA di App\Http\Controllers\QrCodeController (Public)

    /**
     * Display a listing of QR codes.
     */
    public function index(Request $request)
    {
        $query = QrCode::query();

        // Search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('meja', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_tempat', 'like', '%' . $request->search . '%');
            });
        }

        // Status filter (active/inactive)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan jenis QR
        if ($request->filled('filter_type')) {
            switch($request->filter_type) {
                case 'permanent':
                    $query->where('is_permanent', true);
                    break;
                case 'temporary':
                    $query->where('is_permanent', false);
                    break;
                case 'expired':
                    $query->whereNotNull('expired_at')
                          ->where('expired_at', '<', now());
                    break;
            }
        }

        $qrCodes = $query->latest()->paginate(12);

        // Generate QR images
        foreach ($qrCodes as $qrCode) {
            $qrCode->qr_image = $this->generateQrImage($qrCode->code);
        }

        // Stats untuk dashboard
        $stats = [
            'total' => QrCode::count(),
            'active' => QrCode::where('status', 'active')->count(),
            'inactive' => QrCode::where('status', 'inactive')->count(),
            'permanent' => QrCode::where('is_permanent', true)->count(),
            'total_scans' => QrCode::sum('scan_count'),
        ];

        return view('admin.qrcodes.index', compact('qrCodes', 'stats'));
    }

    /**
     * Show form for creating new QR code.
     */
    public function create()
    {
        return view('admin.qrcodes.create');
    }

    /**
     * Store a newly created QR code.
     */
    public function store(Request $request)
    {
        $request->validate([
            'meja' => 'nullable|string|max:50',
            'nama_tempat' => 'nullable|string|max:255',
            'expired_at' => 'nullable|date|after:now',
            'is_permanent' => 'nullable|boolean',
            'notes' => 'nullable|string'
        ]);

        $meja = $request->meja ? str_pad($request->meja, 3, '0', STR_PAD_LEFT) : rand(100, 999);
        $code = 'QR-MEJA-' . date('Ymd') . '-' . $meja;

        while (QrCode::where('code', $code)->exists()) {
            $code = 'QR-MEJA-' . date('Ymd') . '-' . $meja . '-' . rand(10, 99);
        }

        $qrCode = QrCode::create([
            'code' => $code,
            'meja' => $request->meja ? 'Meja ' . $request->meja : null,
            'nama_tempat' => $request->nama_tempat,
            'status' => 'active',
            'expired_at' => $request->is_permanent ? null : $request->expired_at,
            'is_permanent' => $request->is_permanent ?? true,
            'notes' => $request->notes,
            'scan_count' => 0
        ]);

        Cache::forget('qr_codes_count');

        return redirect()->route('admin.qrcodes.index')
            ->with('success', 'QR Code berhasil dibuat!');
    }

    /**
     * Display the specified QR code.
     */
    public function show(QrCode $qrcode)
    {
        $qrImage = $this->generateQrImage($qrcode->code);

        $orderStats = [
            'total' => Order::where('qr_code', $qrcode->code)->count(),
            'active' => Order::where('qr_code', $qrcode->code)
                ->whereIn('order_status', ['waiting', 'processed'])->count(),
            'completed' => Order::where('qr_code', $qrcode->code)
                ->where('order_status', 'completed')->count(),
            'cancelled' => Order::where('qr_code', $qrcode->code)
                ->where('order_status', 'cancelled')->count(),
            'revenue' => Order::where('qr_code', $qrcode->code)
                ->where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('admin.qrcodes.show', compact('qrcode', 'qrImage', 'orderStats'));
    }

    /**
     * Show form for editing QR code.
     */
    public function edit(QrCode $qrcode)
    {
        return view('admin.qrcodes.edit', compact('qrcode'));
    }

    /**
     * Update the specified QR code.
     */
    public function update(Request $request, QrCode $qrcode)
    {
        $request->validate([
            'meja' => 'nullable|string|max:50',
            'nama_tempat' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'expired_at' => 'nullable|date|after:now',
            'is_permanent' => 'nullable|boolean',
            'notes' => 'nullable|string'
        ]);

        $data = [
            'meja' => $request->meja ? 'Meja ' . $request->meja : $qrcode->meja,
            'nama_tempat' => $request->nama_tempat,
            'status' => $request->status,
            'expired_at' => $request->is_permanent ? null : $request->expired_at,
            'is_permanent' => $request->is_permanent ?? true,
            'notes' => $request->notes
        ];

        // Opsi hapus gambar
        if ($request->has('remove_image')) {
            if ($qrcode->image) {
                Storage::disk('public')->delete($qrcode->image);
            }
            $data['image'] = null;
        }

        $qrcode->update($data);

        Cache::forget('qr_codes_count');

        return redirect()->route('admin.qrcodes.index')
            ->with('success', 'QR Code berhasil diupdate!');
    }

    /**
     * Remove the specified QR code.
     */
    public function destroy(QrCode $qrcode)
    {
        $activeOrders = Order::where('qr_code', $qrcode->code)
            ->whereIn('order_status', ['waiting', 'processed'])
            ->exists();

        if ($activeOrders) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak dapat dihapus karena masih digunakan di order aktif'
                ], 400);
            }
            return redirect()->route('admin.qrcodes.index')
                ->with('error', 'QR Code tidak dapat dihapus karena masih digunakan di order aktif');
        }

        // Hapus file gambar jika ada
        if ($qrcode->image) {
            Storage::disk('public')->delete($qrcode->image);
        }

        $qrcode->delete();
        Cache::forget('qr_codes_count');

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'QR Code berhasil dihapus'
            ]);
        }

        return redirect()->route('admin.qrcodes.index')
            ->with('success', 'QR Code berhasil dihapus');
    }

    /**
     * Toggle QR code status.
     */
    public function toggleStatus(QrCode $qrcode)
    {
        $newStatus = $qrcode->status === 'active' ? 'inactive' : 'active';
        $qrcode->update(['status' => $newStatus]);
        Cache::forget('qr_codes_count');

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => 'Status QR Code berhasil diubah menjadi ' . ($newStatus === 'active' ? 'Aktif' : 'Nonaktif')
        ]);
    }

    /**
     * Bulk delete QR codes.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:qr_codes,id'
        ]);

        $deleted = 0;
        $failed = 0;

        foreach ($request->ids as $id) {
            $qrCode = QrCode::find($id);
            if ($qrCode) {
                $activeOrders = Order::where('qr_code', $qrCode->code)
                    ->whereIn('order_status', ['waiting', 'processed'])
                    ->exists();

                if (!$activeOrders) {
                    if ($qrCode->image) {
                        Storage::disk('public')->delete($qrCode->image);
                    }
                    $qrCode->delete();
                    $deleted++;
                } else {
                    $failed++;
                }
            } else {
                $failed++;
            }
        }

        Cache::forget('qr_codes_count');

        return response()->json([
            'success' => true,
            'deleted' => $deleted,
            'failed' => $failed,
            'message' => "Berhasil menghapus $deleted QR code" . ($failed > 0 ? ", $failed gagal karena masih digunakan" : "")
        ]);
    }

    /**
     * Duplicate QR code.
     */
    public function duplicate(QrCode $qrcode)
    {
        $newCode = $qrcode->code . '-copy';
        $counter = 1;
        while (QrCode::where('code', $newCode)->exists()) {
            $newCode = $qrcode->code . '-copy-' . $counter;
            $counter++;
        }

        $newQr = QrCode::create([
            'code' => $newCode,
            'meja' => $qrcode->meja,
            'nama_tempat' => $qrcode->nama_tempat,
            'status' => 'active',
            'expired_at' => $qrcode->expired_at,
            'is_permanent' => $qrcode->is_permanent,
            'notes' => $qrcode->notes,
            'scan_count' => 0
        ]);

        Cache::forget('qr_codes_count');

        return redirect()->route('admin.qrcodes.index')
            ->with('success', 'QR Code berhasil diduplikasi!');
    }

    /**
     * Generate QR code image.
     */
    private function generateQrImage($code)
    {
        try {
            $options = new QROptions([
                'version' => 5,
                'outputType' => QRGenerator::OUTPUT_IMAGE_PNG,
                'eccLevel' => QRGenerator::ECC_L,
                'scale' => 6,
                'imageBase64' => true,
            ]);
            $qr = new QRGenerator($options);
            return $qr->render(route('scan.qr.validate.get', ['qr_code' => $code]));
        } catch (\Exception $e) {
            Log::error('Failed to generate QR image: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Download QR code as PNG.
     */
    public function download(QrCode $qrcode)
    {
        $options = new QROptions([
            'version' => 7,
            'outputType' => QRGenerator::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRGenerator::ECC_L,
            'scale' => 20,
            'imageBase64' => false,
        ]);

        $qr = new QRGenerator($options);
        $qrImage = $qr->render(route('scan.qr.validate.get', ['qr_code' => $qrcode->code]));

        return response($qrImage)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="qrcode-' . $qrcode->code . '.png"');
    }

    /**
     * Print QR code.
     */
    public function print(QrCode $qrcode)
    {
        $options = new QROptions([
            'version' => 7,
            'outputType' => QRGenerator::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRGenerator::ECC_L,
            'scale' => 15,
            'imageBase64' => true,
        ]);
        $qr = new QRGenerator($options);
        $qrImage = $qr->render(route('scan.qr.validate.get', ['qr_code' => $qrcode->code]));

        return view('admin.qrcodes.print', compact('qrcode', 'qrImage'));
    }

    /**
     * Show export page.
     */
    public function exportPage()
    {
        $stats = [
            'total' => QrCode::count(),
            'active' => QrCode::where('status', 'active')->count(),
            'total_scans' => QrCode::sum('scan_count'),
        ];
        
        return view('admin.qrcodes.export', compact('stats'));
    }

    /**
     * Export QR codes to CSV.
     */
    public function exportCsv(Request $request)
    {
        $query = QrCode::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $qrCodes = $query->get();
        $filename = 'qr-codes-export-' . date('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($qrCodes) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, [
                'ID', 'Kode QR', 'Meja', 'Nama Tempat', 'Status', 
                'Scan Count', 'Terakhir Scan', 'Dibuat', 'Kadaluarsa', 'Permanen', 'Catatan'
            ]);

            foreach ($qrCodes as $qr) {
                fputcsv($file, [
                    $qr->id,
                    $qr->code,
                    $qr->meja ?? '-',
                    $qr->nama_tempat ?? '-',
                    $qr->status === 'active' ? 'Aktif' : 'Nonaktif',
                    $qr->scan_count ?? 0,
                    $qr->last_scanned_at ? $qr->last_scanned_at->format('d/m/Y H:i') : '-',
                    $qr->created_at ? $qr->created_at->format('d/m/Y H:i') : '-',
                    $qr->expired_at ? $qr->expired_at->format('d/m/Y H:i') : 'Permanen',
                    $qr->is_permanent ? 'Ya' : 'Tidak',
                    $qr->notes ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export QR codes to Excel.
     */
    public function exportExcel(Request $request)
    {
        $query = QrCode::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $qrCodes = $query->get();
        
        $html = '<table border="1">';
        $html .= '<thead><tr>';
        $html .= '<th>ID</th><th>Kode QR</th><th>Meja</th><th>Nama Tempat</th><th>Status</th>';
        $html .= '<th>Scan Count</th><th>Terakhir Scan</th><th>Dibuat</th><th>Kadaluarsa</th><th>Permanen</th><th>Catatan</th>';
        $html .= '</tr></thead><tbody>';
        
        foreach ($qrCodes as $qr) {
            $html .= '<tr>';
            $html .= '<td>' . $qr->id . '</td>';
            $html .= '<td>' . $qr->code . '</td>';
            $html .= '<td>' . ($qr->meja ?? '-') . '</td>';
            $html .= '<td>' . ($qr->nama_tempat ?? '-') . '</td>';
            $html .= '<td>' . ($qr->status === 'active' ? 'Aktif' : 'Nonaktif') . '</td>';
            $html .= '<td>' . ($qr->scan_count ?? 0) . '</td>';
            $html .= '<td>' . ($qr->last_scanned_at ? $qr->last_scanned_at->format('d/m/Y H:i') : '-') . '</td>';
            $html .= '<td>' . ($qr->created_at ? $qr->created_at->format('d/m/Y H:i') : '-') . '</td>';
            $html .= '<td>' . ($qr->expired_at ? $qr->expired_at->format('d/m/Y H:i') : 'Permanen') . '</td>';
            $html .= '<td>' . ($qr->is_permanent ? 'Ya' : 'Tidak') . '</td>';
            $html .= '<td>' . ($qr->notes ?? '-') . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table>';
        
        $filename = 'qr-codes-export-' . date('Y-m-d-H-i-s') . '.xls';
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        return response($html, 200, $headers);
    }

    /**
     * Export QR codes to PDF.
     */
    public function exportPdf(Request $request)
    {
        $query = QrCode::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $qrCodes = $query->get();
        
        $html = view('admin.qrcodes.export-pdf', compact('qrCodes'))->render();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('qr-codes-export-' . date('Y-m-d') . '.pdf');
    }
}