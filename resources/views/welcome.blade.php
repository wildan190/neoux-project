<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyApp ERP — Solusi ERP Modern</title>

    @vite('resources/css/app.css')
    <script src="https://unpkg.com/feather-icons"></script>

    {{-- AOS Animation --}}
    <link href="https://unpkg.com/aos@next/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-all duration-300">

    {{-- NAVBAR --}}
    <nav class="fixed top-0 left-0 right-0 bg-white/70 dark:bg-gray-900/70 backdrop-blur z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-2xl font-extrabold text-primary-600 dark:text-primary-400">MyApp ERP</div>

            <div class="hidden md:flex items-center gap-8 font-medium">
                <a href="#features" class="hover:text-primary-500">Fitur</a>
                <a href="#benefits" class="hover:text-primary-500">Kelebihan</a>
                <a href="#compare" class="hover:text-primary-500">Perbandingan</a>
                <a href="#pricing" class="hover:text-primary-500">Harga</a>
            </div>

            <div>
                <a href="/login" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg shadow">
                    Login
                </a>
            </div>
        </div>
    </nav>

    {{-- HERO --}}
    <section class="pt-32 pb-24 px-6">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-10 items-center">

            <div data-aos="fade-right">
                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-6">
                    ERP Modern untuk Bisnis
                    <span class="text-primary-600 dark:text-primary-400">Lebih Efisien & Terukur</span>
                </h1>

                <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">
                    MyApp ERP membantu perusahaan mengelola operasional dengan lebih cepat, terintegrasi,
                    serta memiliki data real-time yang mudah dipahami. Dibangun dengan teknologi terbaru
                    dan desain minimalis untuk produktivitas maksimal.
                </p>

                <div class="flex gap-4">
                    <a href="/register"
                        class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow">
                        Coba Gratis
                    </a>

                    <a href="#features"
                        class="px-6 py-3 border border-primary-600 text-primary-600 hover:bg-primary-50 dark:hover:bg-gray-800 rounded-xl font-semibold">
                        Jelajahi Fitur
                    </a>
                </div>
            </div>

            <div data-aos="fade-left" class="relative">
                <img src="https://images.unsplash.com/photo-1551033406-611cf9a28f67?auto=format&fit=crop&w=1100&q=80"
                    class="rounded-2xl shadow-xl border dark:border-gray-700" alt="ERP Dashboard">
            </div>

        </div>
    </section>


    {{-- FEATURES --}}
    <section id="features" class="py-24 bg-gray-50 dark:bg-gray-800/40">
        <div class="max-w-7xl mx-auto px-6">
            <h2 data-aos="fade-up" class="text-3xl font-bold text-center mb-14">Fitur Utama MyApp ERP</h2>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">

                <div data-aos="zoom-in" class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow">
                    <i data-feather="layout" class="w-10 h-10 text-primary-500 mb-4"></i>
                    <h3 class="font-bold text-xl mb-2">Dashboard Real-Time</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Grafik KPI, penjualan, pembelian, dan inventory terpusat dalam satu tampilan.
                    </p>
                </div>

                <div data-aos="zoom-in" class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow">
                    <i data-feather="shopping-cart" class="w-10 h-10 text-primary-500 mb-4"></i>
                    <h3 class="font-bold text-xl mb-2">Penjualan & Invoice</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Kelola order, pelanggan, invoice, delivery, dan laporan otomatis.
                    </p>
                </div>

                <div data-aos="zoom-in" class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow">
                    <i data-feather="box" class="w-10 h-10 text-primary-500 mb-4"></i>
                    <h3 class="font-bold text-xl mb-2">Inventori & Stok</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Pantau stok akurat, tracking barang, multi-gudang, hingga auto reorder.
                    </p>
                </div>

                <div data-aos="zoom-in" class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow">
                    <i data-feather="file-text" class="w-10 h-10 text-primary-500 mb-4"></i>
                    <h3 class="font-bold text-xl mb-2">Laporan Keuangan</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Jurnal otomatis, neraca, arus kas, profit-loss, dan laporan pajak.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center px-6">

            <div class="md:order-2">
                <img src="https://images.unsplash.com/photo-1522199710521-72d69614c702?q=80&w=1200"
                    alt="Digital catalogue system" class="rounded-2xl shadow-xl w-full object-cover" />
            </div>

            <div class="md:order-1">
                <h2 class="text-3xl font-bold text-primary-500 dark:text-primary-300 mb-4">
                    e-Catalogue Digital untuk Manajemen Produk yang Lebih Efisien
                </h2>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">
                    Fitur <strong>e-Catalogue MyApp ERP</strong> dirancang untuk membantu perusahaan
                    menampilkan seluruh produk atau layanan secara terstruktur dan mudah diakses...
                </p>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                    Sistem ini mendukung pengelolaan foto, spesifikasi, harga, kategori...
                </p>
            </div>

        </div>
    </section>

    <section class="py-24 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center px-6">

            <div class="md:order-1">
                <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=1200"
                    alt="Procurement automation system" class="rounded-2xl shadow-xl w-full object-cover" />
            </div>

            <div class="md:order-2">
                <h2 class="text-3xl font-bold text-primary-500 dark:text-primary-300 mb-4">
                    e-Procurement dengan Pengadaan Otomatis
                </h2>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">
                    Fitur <strong>e-Procurement</strong> pada MyApp ERP menghadirkan proses pengadaan...
                </p>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                    Dengan otomasi yang terintegrasi, perusahaan dapat mengurangi human error...
                </p>
            </div>

        </div>
    </section>

    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center px-6">

            <div class="md:order-2">
                <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=1200"
                    alt="Vendor management dashboard" class="rounded-2xl shadow-xl w-full object-cover" />
            </div>

            <div class="md:order-1">
                <h2 class="text-3xl font-bold text-primary-500 dark:text-primary-300 mb-4">
                    Manajemen Vendor yang Lebih Terstruktur
                </h2>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">
                    Sistem <strong>Vendor Management MyApp ERP</strong> memudahkan perusahaan...
                </p>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                    Fitur ini membantu meningkatkan kualitas hubungan dengan pemasok...
                </p>
            </div>

        </div>
    </section>

    {{-- BENEFITS / KELEBIHAN --}}
    <section id="benefits" class="py-24 px-6">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16">

            <div data-aos="fade-right">
                <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1100&q=80"
                    class="rounded-2xl shadow-lg border dark:border-gray-700" alt="People working">
            </div>

            <div data-aos="fade-left" class="flex flex-col justify-center">
                <h2 class="text-3xl font-bold mb-6">Mengapa Memilih <span class="text-primary-600">MyApp ERP?</span>
                </h2>

                <ul class="space-y-4 text-gray-600 dark:text-gray-300 text-lg">
                    <li class="flex gap-3">
                        <i data-feather="check-circle" class="w-6 h-6 text-green-500"></i>
                        Integrasi penuh antar modul tanpa repot export-import data.
                    </li>
                    <li class="flex gap-3">
                        <i data-feather="check-circle" class="w-6 h-6 text-green-500"></i>
                        Desain modern, cepat, dan nyaman digunakan.
                    </li>
                    <li class="flex gap-3">
                        <i data-feather="check-circle" class="w-6 h-6 text-green-500"></i>
                        Laporan otomatis tanpa perlu input manual.
                    </li>
                    <li class="flex gap-3">
                        <i data-feather="check-circle" class="w-6 h-6 text-green-500"></i>
                        Support lokal berbahasa Indonesia.
                    </li>
                </ul>

            </div>

        </div>
    </section>


    {{-- COMPARISON SECTION --}}
    <section class="py-24 bg-white dark:bg-gray-800" id="comparison">
        <div class="max-w-7xl mx-auto px-6">

            <h2 class="text-3xl md:text-4xl font-extrabold text-center text-gray-900 dark:text-white mb-8"
                data-aos="fade-up">
                Dibandingkan dengan kompetitor — kenapa kami lebih unggul?
            </h2>

            <p class="text-center text-gray-600 dark:text-gray-300 mb-16 max-w-3xl mx-auto" data-aos="fade-up"
                data-aos-delay="150">
                Berikut perbandingan jujur & spesifik dengan platform ERP populer di pasaran.
            </p>

            <div class="overflow-x-auto rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="250">
                <table class="w-full text-left border-collapse bg-white dark:bg-gray-900">
                    <thead class="bg-primary-600 text-white">
                        <tr>
                            <th class="p-4">Fitur</th>
                            <th class="p-4">MyERP</th>
                            <th class="p-4">Odoo</th>
                            <th class="p-4">ERPNext</th>
                            <th class="p-4">Accurate</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-700 dark:text-gray-300">

                        <tr>
                            <td class="p-4 font-semibold">Harga per Bulan</td>
                            <td class="p-4 text-green-500 font-bold">Mulai Rp 199K</td>
                            <td class="p-4">± Rp 250K</td>
                            <td class="p-4">± Rp 300K</td>
                            <td class="p-4">± Rp 299K</td>
                        </tr>

                        <tr>
                            <td class="p-4 font-semibold">Custom Modul</td>
                            <td class="p-4 text-green-500 font-bold">✔ Sangat Fleksibel</td>
                            <td class="p-4">✔ Ada</td>
                            <td class="p-4">✔ Ada</td>
                            <td class="p-4 text-red-500">✖ Tidak fleksibel</td>
                        </tr>

                        <tr>
                            <td class="p-4 font-semibold">Kecepatan Dashboard</td>
                            <td class="p-4 text-green-500 font-bold">0.2s Load</td>
                            <td class="p-4">0.6s</td>
                            <td class="p-4">0.7s</td>
                            <td class="p-4">0.8s</td>
                        </tr>

                        <tr>
                            <td class="p-4 font-semibold">Antarmuka Mobile</td>
                            <td class="p-4 text-green-500 font-bold">✔ Fully Responsive</td>
                            <td class="p-4 text-yellow-500">• Cukup</td>
                            <td class="p-4 text-yellow-500">• Cukup</td>
                            <td class="p-4 text-red-500">✖ Tidak optimal</td>
                        </tr>

                        <tr>
                            <td class="p-4 font-semibold">Integrasi API</td>
                            <td class="p-4 text-green-500 font-bold">✔ Unlimited API</td>
                            <td class="p-4">✔</td>
                            <td class="p-4">✔</td>
                            <td class="p-4 text-red-500">✖ Tidak ada</td>
                        </tr>

                        <tr>
                            <td class="p-4 font-semibold">Dukungan</td>
                            <td class="p-4 text-green-500 font-bold">24/7 + Engineer</td>
                            <td class="p-4">Jam kerja</td>
                            <td class="p-4">Jam kerja</td>
                            <td class="p-4">Jam kerja</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </section>


    {{-- PRICING SECTION - ADVANCED VERSION --}}
    <section class="py-28 bg-gray-50 dark:bg-gray-900" id="pricing">
        <div class="max-w-7xl mx-auto px-6">

            <!-- TITLE -->
            <h2 class="text-4xl md:text-5xl font-extrabold text-center text-gray-900 dark:text-white mb-6"
                data-aos="fade-up">
                Pilih Paket yang Sesuai Dengan Tahap Bisnis Anda
            </h2>

            <p class="text-center text-gray-600 dark:text-gray-300 mb-20 max-w-3xl mx-auto" data-aos="fade-up"
                data-aos-delay="150">
                Semua paket kami dirancang untuk mendukung bisnis dari skala kecil hingga enterprise—tanpa biaya
                tersembunyi,
                tanpa kontrak panjang, dan bisa upgrade kapan pun.
            </p>

            <!-- PRICING CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

                {{-- BASIC PLAN --}}
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-3xl p-10 border border-gray-200 dark:border-gray-700 hover:-translate-y-2 hover:shadow-2xl transition-transform"
                    data-aos="zoom-in">

                    <h3 class="text-2xl font-extrabold mb-2">Basic</h3>
                    <p class="text-gray-500 dark:text-gray-300 mb-6">
                        Paket paling terjangkau untuk UMKM yang baru mulai digitalisasi proses bisnis.
                    </p>

                    <h4 class="text-4xl font-extrabold text-primary-600 mb-2">
                        Rp 199.000<span class="text-lg font-medium text-gray-500">/bulan</span>
                    </h4>

                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Tanpa biaya setup • Bisa dibatalkan kapan
                        saja</p>

                    <ul class="space-y-3 text-gray-700 dark:text-gray-300 text-sm">
                        <li>✔ Manajemen Penjualan</li>
                        <li>✔ Manajemen Stok</li>
                        <li>✔ Laporan Dasar</li>
                        <li>✔ 2 Pengguna Aktif</li>
                        <li>✔ Support via Email (Jam Kerja)</li>
                        <li class="text-red-400">✖ Tidak termasuk API</li>
                        <li class="text-red-400">✖ Tidak termasuk Produksi & Pembelian</li>
                        <li class="text-red-400">✖ Tidak ada mobile app</li>
                    </ul>

                    <hr class="my-6 border-gray-300 dark:border-gray-700">

                    <p class="font-semibold text-gray-800 dark:text-gray-200">Cocok untuk:</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Toko kecil, retail sederhana, online shop, dan usaha rumahan.
                    </p>

                    <button
                        class="mt-8 w-full py-3 px-4 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition">
                        Mulai Paket Basic
                    </button>
                </div>



                {{-- PROFESSIONAL PLAN --}}
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-3xl p-10 border-2 border-primary-600 hover:-translate-y-2 hover:shadow-2xl transition-transform relative overflow-hidden"
                    data-aos="zoom-in" data-aos-delay="150">

                    <span
                        class="absolute top-0 right-0 bg-primary-600 text-white px-5 py-1 rounded-bl-xl text-xs font-bold">
                        TERLARIS
                    </span>

                    <h3 class="text-2xl font-extrabold mb-2">Professional</h3>
                    <p class="text-gray-500 dark:text-gray-300 mb-6">
                        Solusi lengkap untuk bisnis berkembang yang butuh automasi dan integrasi antar divisi.
                    </p>

                    <h4 class="text-4xl font-extrabold text-primary-600 mb-2">
                        Rp 499.000<span class="text-lg font-medium text-gray-500">/bulan</span>
                    </h4>

                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Termasuk semua modul bisnis utama</p>

                    <ul class="space-y-3 text-gray-700 dark:text-gray-300 text-sm">
                        <li>✔ Semua fitur Basic</li>
                        <li>✔ Modul Pembelian</li>
                        <li>✔ Modul Produksi</li>
                        <li>✔ Modul Keuangan & Akuntansi</li>
                        <li>✔ Real-time Analytics Dashboard</li>
                        <li>✔ 10 Pengguna Aktif</li>
                        <li>✔ API Access + Webhook</li>
                        <li>✔ Backup Otomatis Harian</li>
                        <li>✔ Mobile App Full Version</li>
                        <li class="text-yellow-400">• Integrasi Marketplace (Add-on)</li>
                    </ul>

                    <hr class="my-6 border-gray-300 dark:border-gray-700">

                    <p class="font-semibold text-gray-800 dark:text-gray-200">Cocok untuk:</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Perusahaan yang tumbuh pesat dan butuh efisiensi proses operasional.
                    </p>

                    <button
                        class="mt-8 w-full py-3 px-4 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition">
                        Pilih Professional
                    </button>
                </div>



                {{-- ENTERPRISE PLAN --}}
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-3xl p-10 border border-gray-200 dark:border-gray-700 hover:-translate-y-2 hover:shadow-2xl transition-transform"
                    data-aos="zoom-in" data-aos-delay="300">

                    <h3 class="text-2xl font-extrabold mb-2">Enterprise</h3>
                    <p class="text-gray-500 dark:text-gray-300 mb-6">
                        Paket premium untuk perusahaan besar dengan proses kompleks dan integrasi tingkat tinggi.
                    </p>

                    <h4 class="text-4xl font-extrabold text-primary-600 mb-2">
                        Custom
                    </h4>

                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Dapat disesuaikan dengan workflow bisnis
                    </p>

                    <ul class="space-y-3 text-gray-700 dark:text-gray-300 text-sm">
                        <li>✔ Custom Modul (SOP / Workflow)</li>
                        <li>✔ Integrasi ERP lama</li>
                        <li>✔ Integrasi IoT & Warehouse Automation</li>
                        <li>✔ Unlimited Pengguna</li>
                        <li>✔ Dedicated Success Manager</li>
                        <li>✔ SLA 99.9% Uptime</li>
                        <li>✔ Deployment Cloud / On-Premise</li>
                        <li>✔ Security Audit + Hardening</li>
                        <li>✔ Prioritas 24/7 Support Engineer</li>
                    </ul>

                    <hr class="my-6 border-gray-300 dark:border-gray-700">

                    <p class="font-semibold text-gray-800 dark:text-gray-200">Cocok untuk:</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Pabrik, distributor skala besar, manufaktur kompleks, retail chain multi-cabang.
                    </p>

                    <button
                        class="mt-8 w-full py-3 px-4 bg-gray-900 text-white rounded-xl hover:bg-gray-700 transition">
                        Hubungi Kami
                    </button>
                </div>

            </div>
        </div>
    </section>


    {{-- FOOTER --}}
    <footer class="py-10 bg-gray-900 text-gray-300 text-center">
        <p>&copy; {{ date('Y') }} MyApp ERP — Dibuat dengan ❤️ untuk bisnis Indonesia.</p>
    </footer>


    {{-- Scripts --}}
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 900, once: true });
        feather.replace();
    </script>

</body>

</html>