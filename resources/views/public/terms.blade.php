@extends('layouts.public')
@section('title', 'Syarat dan Ketentuan')

@section('content')
<div class="bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-gray-100">
    <h1 class="text-3xl md:text-4xl font-bold mb-8 text-gray-900">Syarat dan Ketentuan</h1>
    <div class="space-y-6 text-gray-600 leading-relaxed">
        <p class="text-sm bg-blue-50 text-blue-700 px-4 py-2 rounded-lg inline-block font-medium">Terakhir diperbarui: {{ date('d F Y') }}</p>
        
        <div class="pt-4">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">1. Pendahuluan</h2>
            <p>Selamat datang di sistem aplikasi Point of Sale (POS) kami. Dengan mengakses, melihat, atau menggunakan aplikasi kami, Anda secara otomatis menyetujui syarat dan ketentuan yang berlaku di bawah ini. Harap membaca seluruh ketentuan ini dengan saksama sebelum melanjutkan transaksi.</p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">2. Layanan Sistem Kami</h2>
            <p>Sistem ini dirancang khusus untuk memfasilitasi transaksi digital dan manajemen operasional restoran kami. Sistem kami memungkinkan penerimaan pesanan, perhitungan biaya (termasuk pajak/layanan), dan pemrosesan pembayaran melalui berbagai metode seperti QRIS, Virtual Account, dan Tunai.</p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">3. Kebijakan Transaksi dan Pembayaran</h2>
            <p>Setiap transaksi pembayaran yang telah berhasil diproses melalui sistem kami dianggap final. Segala bentuk pesanan makanan atau minuman yang telah dibayarkan <strong>tidak dapat dibatalkan atau di-refund</strong> secara sepihak oleh pelanggan, kecuali terdapat kondisi khusus (misalnya pesanan rusak/salah) yang disepakati langsung oleh manajemen restoran di lokasi.</p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">4. Ketersediaan Stok & Keterangan "Sold Out"</h2>
            <p>Sistem inventaris kami tersinkronisasi secara otomatis dengan dapur restoran kami. Apabila Anda melihat keterangan <strong>"Sold Out"</strong> atau "Stok Habis" pada produk tertentu, itu menandakan bahwa bahan baku untuk produk tersebut benar-benar habis di dapur dan tidak dapat dipesan. Hal ini ditujukan untuk mencegah penerimaan pesanan yang tidak dapat kami proses.</p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">5. Pembaruan Syarat & Ketentuan</h2>
            <p>Kami berhak penuh untuk merevisi, memodifikasi, atau mengubah syarat dan ketentuan ini sewaktu-waktu sesuai dengan kebutuhan bisnis atau kepatuhan terhadap hukum yang berlaku. Pengguna diharapkan untuk rutin meninjau halaman ini.</p>
        </div>
    </div>
</div>
@endsection
