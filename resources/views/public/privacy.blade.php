@extends('layouts.public')
@section('title', 'Kebijakan Privasi')

@section('content')
<div class="bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-gray-100">
    <h1 class="text-3xl md:text-4xl font-bold mb-8 text-gray-900">Kebijakan Privasi</h1>
    <div class="space-y-6 text-gray-600 leading-relaxed">
        <p class="text-sm bg-blue-50 text-blue-700 px-4 py-2 rounded-lg inline-block font-medium">Terakhir diperbarui: {{ date('d F Y') }}</p>
        
        <div class="pt-4">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">1. Pengumpulan Informasi Pribadi</h2>
            <p>Sistem POS restoran kami sangat menghargai privasi Anda. Kami hanya mengumpulkan data yang secara langsung dibutuhkan untuk memproses pesanan dan mengelola reservasi Anda, seperti nama, nomor meja, detail pesanan, dan kadang-kadang nomor telepon atau email jika Anda membutuhkan struk pembayaran digital.</p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">2. Penggunaan Data Transaksi</h2>
            <p>Data yang kami kumpulkan dari Anda digunakan secara eksklusif untuk:</p>
            <ul class="list-disc pl-5 mt-2 space-y-1">
                <li>Melakukan verifikasi dan pencatatan pembayaran transaksi.</li>
                <li>Menghubungkan pembayaran ke pesanan di dapur.</li>
                <li>Menerbitkan dan mengirimkan invoice/struk digital (jika ada permintaan).</li>
                <li>Analisis laporan internal restoran demi peningkatan layanan.</li>
            </ul>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">3. Keamanan dan Perlindungan Data</h2>
            <p>Kami bekerja sama dengan penyedia Payment Gateway pihak ketiga yang resmi, berlisensi oleh Bank Indonesia, dan menggunakan standar enkripsi PCI-DSS tertinggi. Data sensitif seperti kredensial perbankan atau PIN tidak pernah kami simpan atau akses secara langsung di server lokal restoran kami.</p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">4. Pembagian Informasi ke Pihak Ketiga</h2>
            <p>Kami tidak memperjualbelikan, menyewakan, atau menyalahgunakan informasi pribadi pelanggan kami kepada pihak manapun. Pembagian data hanya terjadi dalam ruang lingkup pemrosesan pembayaran melalui mitra Payment Gateway, atau jika secara spesifik diwajibkan oleh proses hukum resmi yang berlaku di Republik Indonesia.</p>
        </div>
    </div>
</div>
@endsection
