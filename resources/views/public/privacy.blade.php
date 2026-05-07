@extends('layouts.public')
@section('title', 'Kebijakan Privasi')

@section('content')
<div class="rounded-2xl border border-yellow-500/16 bg-[#171717] p-8 md:p-10 shadow-sm">
    <h1 class="font-display text-3xl md:text-4xl font-bold mb-8 text-white">Kebijakan Privasi</h1>
    <div class="space-y-8 text-white/70 leading-relaxed text-lg">
        <p class="text-sm border border-yellow-500/20 bg-yellow-500/10 text-yellow-500 px-4 py-2 rounded-lg inline-block font-medium">Terakhir diperbarui: {{ date('d F Y') }}</p>
        
        <div>
            <h2 class="text-2xl font-semibold text-white mb-3">1. Pengumpulan Informasi Pribadi</h2>
            <p>Sistem POS restoran kami sangat menghargai privasi Anda. Kami hanya mengumpulkan data yang secara langsung dibutuhkan untuk memproses pesanan dan mengelola reservasi Anda, seperti nama, nomor meja, detail pesanan, dan kadang-kadang nomor telepon atau email jika Anda membutuhkan struk pembayaran digital.</p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold text-white mb-3">2. Penggunaan Data Transaksi</h2>
            <p>Data yang kami kumpulkan dari Anda digunakan secara eksklusif untuk:</p>
            <ul class="list-disc pl-5 mt-3 space-y-2 text-white/70">
                <li>Melakukan verifikasi dan pencatatan pembayaran transaksi.</li>
                <li>Menghubungkan pembayaran ke pesanan di dapur.</li>
                <li>Menerbitkan dan mengirimkan invoice/struk digital (jika ada permintaan).</li>
                <li>Analisis laporan internal restoran demi peningkatan layanan.</li>
            </ul>
        </div>

        <div>
            <h2 class="text-2xl font-semibold text-white mb-3">3. Keamanan dan Perlindungan Data</h2>
            <p>Kami bekerja sama dengan penyedia Payment Gateway pihak ketiga yang resmi, berlisensi oleh Bank Indonesia, dan menggunakan standar enkripsi PCI-DSS tertinggi. Data sensitif seperti kredensial perbankan atau PIN tidak pernah kami simpan atau akses secara langsung di server lokal restoran kami.</p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold text-white mb-3">4. Pembagian Informasi ke Pihak Ketiga</h2>
            <p>Kami tidak memperjualbelikan, menyewakan, atau menyalahgunakan informasi pribadi pelanggan kami kepada pihak manapun. Pembagian data hanya terjadi dalam ruang lingkup pemrosesan pembayaran melalui mitra Payment Gateway, atau jika secara spesifik diwajibkan oleh proses hukum resmi yang berlaku di Republik Indonesia.</p>
        </div>
    </div>
</div>
@endsection
