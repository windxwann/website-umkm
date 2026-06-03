# Dokumentasi Sistem: Dapoer Jiemas QR Order

## 1. Latar Belakang Masalah
Dalam industri kuliner modern (restoran/kafe), efisiensi waktu dan akurasi pesanan adalah kunci utama kepuasan pelanggan. Sistem pemesanan konvensional dimana pelanggan harus menunggu pelayan datang membawa buku menu seringkali menimbulkan beberapa masalah:
- **Antrean dan Waktu Tunggu:** Pelanggan membuang waktu menunggu pelayan, terutama saat jam sibuk (*peak hours*).
- **Human Error:** Risiko kesalahan pencatatan pesanan oleh pelayan atau salah dengar.
- **Keterbatasan Fisik Menu:** Buku menu cetak sulit diperbarui jika ada perubahan harga atau jika suatu menu sedang habis.
- **Penumpukan di Kasir:** Pelanggan menumpuk di meja kasir hanya untuk memesan dan membayar.

## 2. Solusi yang Ditawarkan
Sistem **Dapoer Jiemas QR Order** hadir sebagai solusi digital (*Self-Service Ordering System*) berbasis web. 
Alih-alih memanggil pelayan, pelanggan cukup **memindai (scan) QR Code** yang tertempel di meja mereka menggunakan *smartphone* masing-masing. Mereka dapat melihat menu digital interaktif, memasukkan ke keranjang, dan langsung melakukan *checkout* pesanan. Pesanan tersebut akan seketika muncul di layar Kasir dan Dapur untuk segera diproses.

## 3. Keunggulan Sistem (Fitur Unggulan)
Sistem ini tidak hanya sekadar menampilkan menu digital, tetapi dilengkapi dengan fitur-fitur canggih setara kelas *enterprise*:

1. **Tanpa Install Aplikasi (Web-Based):** Pelanggan tidak perlu mengunduh aplikasi apapun. Cukup buka kamera HP dan scan QR Code.
2. **Keamanan Sesi Ketat (Strict Session Lock - *Satpam Digital*):** Sistem mengunci sesi QR Code ke perangkat pelanggan pertama yang memindainya. Hal ini mencegah *Spam Order* dari pelanggan iseng yang mencoba memesan dari luar restoran (dari rumah).
3. **Manajemen Stok Otomatis:** Stok menu di dapur terintegrasi penuh. Saat pelanggan memesan, stok akan terkunci. Jika kasir membatalkan pesanan, stok akan otomatis kembali ke sistem.
4. **Validasi Harga Sisi Server (Anti-Hacking):** Sistem menghitung total tagihan murni dari *database* rahasia milik server, sehingga kebal terhadap manipulasi harga oleh *hacker* melalui *Inspect Element* browser.
5. **Real-Time Order Tracking:** Pelanggan dapat memantau status pesanan mereka secara *live* di HP (Menunggu → Diproses → Selesai) tanpa harus bolak-balik bertanya ke kasir.

## 4. Penjelasan Modul Sistem

### A. Modul Pelanggan (Customer Front-End)
- **Scan QR & Menu Digital:** Memasuki sistem melalui *scan* QR. Menampilkan menu lengkap beserta kategori, gambar, deskripsi, dan harga. Jika menu habis, otomatis tidak bisa dipesan.
- **Keranjang (Cart) & Checkout:** Pelanggan merangkum pesanan, memasukkan nama, dan mengirim pesanan ke kasir.
- **Live Tracking:** Halaman pemantauan untuk melihat rincian tagihan dan pergerakan status pesanan secara *real-time*.

### B. Modul Kasir (Cashier Dashboard)
- **Manajemen Antrean (POS):** Layar kasir yang menampilkan pesanan masuk secara urut. Kasir dapat mengubah status pesanan dari "Menunggu" menjadi "Diproses".
- **Pembayaran & Struk:** Memproses pembayaran (Tunai/Non-Tunai), menghitung kembalian, dan mencetak struk digital (*Receipt*).
- **Edit Pesanan:** Mengakomodasi jika ada pelanggan yang ingin tambah/kurangi pesanan setelah pesanan masuk.
- **Reset Meja:** Setelah pelanggan selesai makan dan pergi, kasir mereset meja agar QR Code bisa dipindai oleh pelanggan berikutnya.

### C. Modul Administrator (Admin Back-End)
- **Kelola Produk & Kategori (Master Data):** Menambah, mengedit, menghapus foto dan deskripsi menu beserta harganya.
- **Kelola QR Code / Meja:** Mencetak (Print) QR Code baru untuk meja baru, atau mencabut akses meja tertentu.
- **Laporan & Analitik:** Melihat grafik pendapatan, total penjualan per metode pembayaran, menu terlaris, dan mengekspor (Download) laporan keuangan dalam bentuk PDF/Excel untuk evaluasi bisnis.
