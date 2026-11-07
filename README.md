# ☕ Sistem Pendukung Keputusan (SPK) - Metode SAW (Kelompok 2)

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![HTML/CSS](https://img.shields.io/badge/HTML5_&_CSS3-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![Chart.js](https://img.shields.io/badge/Chart.js-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white)

Aplikasi berbasis web untuk menyelesaikan masalah pengambilan keputusan **Multi-Attribute Decision Making (MADM)** menggunakan algoritma **Simple Additive Weighting (SAW)**. 

Berawal dari studi kasus pemilihan supplier biji kopi terbaik untuk "Kedai Kopi Senja", aplikasi ini telah dikembangkan menjadi sistem **Full CRUD yang dinamis**. Sistem ini mampu menangani berbagai studi kasus (multi-periode) dengan kriteria, bobot, tipe atribut (Benefit/Cost), dan jumlah alternatif yang tidak terbatas.

---

## ✨ Fitur Utama

- **Multi-Studi Kasus (Periode):** Mendukung pembuatan banyak sesi seleksi yang independen.
- **Manajemen Kriteria Dinamis:** Tambah, edit, dan hapus kriteria. Mendukung penentuan tipe atribut (*Benefit* atau *Cost*) dan pembobotan secara fleksibel.
- **Manajemen Kandidat (Alternatif):** Kelola data kandidat yang terikat secara spesifik pada masing-masing periode seleksi.
- **Matriks Penilaian Interaktif:** Form input nilai mentah (*Matriks Keputusan*) yang secara otomatis menyesuaikan dengan jumlah kriteria dan kandidat.
- **Live Calculation SAW:** Mesin komputasi yang melakukan pencarian nilai ekstrem (Max/Min), normalisasi matriks (R), dan perhitungan nilai preferensi (V) secara instan.
- **Dashboard Visual:** Dilengkapi dengan visualisasi data interaktif menggunakan *Chart.js* untuk menampilkan *Top 5* kandidat terbaik.

---

## 👥 Tim Pengembang (Kelompok 2)

Proyek ini disusun untuk memenuhi tugas **Project Akhir Praktikum Sistem Pendukung Keputusan**.

| Nama Lengkap | NIM |
| :--- | :--- |
| **Zyrus Alfredo Randan Malinggato** | `2409116120` |
| **Jemis Movid** | `2409116070` |
| **Maria Claudia Meo** | `2409116108` |
| **Fathinatuz Zaina** | `2409116016` |
| **Dimas Aji Mukti** | `2409116107` |

---

## 🛠️ Persyaratan Sistem (Prerequisites)

Untuk menjalankan aplikasi ini di lingkungan lokal (*localhost*), Anda memerlukan:
- Web Server (Apache) - direkomendasikan menggunakan **Laragon** atau **XAMPP**.
- PHP (Versi 7.4 atau lebih baru).
- MySQL / MariaDB.

---

## 🚀 Panduan Instalasi (Cara Menjalankan)

1. **Clone Repository**
   Buka terminal/CMD dan jalankan perintah berikut di dalam direktori `www` (Laragon) atau `htdocs` (XAMPP):
   ```bash
   git clone https://github.com/username-anda/nama-repo-anda.git
   ```

2. **Siapkan Database**
   - Buka `phpMyAdmin` (biasanya di `http://localhost/phpmyadmin`).
   - Buat database baru dengan nama: `db_spk_saw`.
   - *Import* file SQL (jika ada di dalam repository) atau jalankan query pembuatan 4 tabel utama (`periode`, `kriteria`, `alternatif`, `penilaian`) sesuai rancangan relasional.

3. **Konfigurasi Koneksi**
   Pastikan kredensial database pada file `koneksi.php` sudah sesuai dengan environment Anda:
   ```php
   $host = "localhost";
   $user = "root"; 
   $pass = "";     
   $db   = "db_spk_saw";
   ```

4. **Jalankan Aplikasi**
   Buka web browser dan akses URL berikut:
   ```text
   http://localhost/nama-folder-project/index.php
   ```

---

## 📂 Struktur Direktori Proyek

```text
📁 root-project/
├── 📄 koneksi.php      # Konfigurasi penghubung PHP ke MySQL
├── 📄 index.php        # Halaman Dashboard Utama (Statistik & Chart)
├── 📄 periode.php      # Modul CRUD Periode / Studi Kasus
├── 📄 kriteria.php     # Modul CRUD Kriteria (Atribut & Bobot)
├── 📄 alternatif.php   # Modul CRUD Daftar Kandidat Supplier
├── 📄 penilaian.php    # Modul Input Matriks Keputusan Mentah
└── 📄 hitung.php       # Mesin Eksekusi Normalisasi & Perankingan SAW
```

---
*Kelompok 2 - Praktikum Proyek Akhir Sistem Pendukung Keputusan*
