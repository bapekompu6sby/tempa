# TEMPA — Panduan Pengguna

Panduan ini menjelaskan cara menggunakan aplikasi TEMPA (label UI menggunakan bahasa Indonesia). Mencakup: mulai dari halaman utama, masuk sebagai panitia, membuat pelatihan, memeriksa instruksi pelatihan, dan mengunggah dokumen pelatihan.

---

## Sekilas

- Halaman utama: menampilkan hero TEMPA dan form password untuk panitia. Tombol: `Masuk Panitia`.
- Setelah berhasil masuk, header akan menampilkan tautan: `Instruksi` dan `Pelatihan`.
- Daftar pelatihan (event) ditampilkan sebagai kartu dengan badge learning model dan tiga blok fase (Persiapan / Pelaksanaan / Pelaporan).
- Editor dokumen menggunakan mekanisme staged upload: file yang dipilih disimpan sementara di sisi klien dan baru diunggah ketika menekan `Simpan`.

---

## 1) Buka aplikasi dan masuk (halaman utama)

1. Buka URL root aplikasi di browser Anda.
2. Temukan input password dengan label: **Masukkan Password untuk panitia**.
3. Ketik password panitia lalu klik tombol biru **Masuk Panitia**.
   - Setelah berhasil, Anda akan melihat tautan `Instruksi` dan `Pelatihan` di header.
   - Jika Anda tidak memiliki password, daftar pelatihan yang bersifat tampilan-baca akan muncul di bawah form login.

Tip: jika Anda mengubah view dan browser masih menampilkan UI lama, jalankan dari root proyek:

```cmd
php artisan view:clear
```

---

## 2) Membuat pelatihan baru (Pelatihan)

1. Klik **Pelatihan** pada header atau buka halaman **Daftar Pelatihan**.
2. Klik tombol `Buat`, `Tambah`, atau ikon `+` untuk membuat event/pelatihan baru (label bisa berbeda tergantung UI).
3. Isi field yang diperlukan pada form event:
   - Nama Pelatihan
   - Tanggal Mulai (Start date) / Tanggal Selesai (End date)
   - Learning model (pilih: E-Learning / Distance / Blended / Klasikal)
   - Catatan (opsional)
4. Simpan/Kirim form.

Catatan:
- Aplikasi mungkin secara otomatis menghitung dua tanggal bantu saat event dibuat:
  - `preparation_date` = start_date dikurangi 30 hari (digunakan untuk fase Persiapan)
  - `report_date` = end_date ditambah 14 hari (digunakan untuk fase Pelaporan)
- Jika kolom-kolom tersebut belum tersedia, jalankan migrasi (khusus admin/maintenance):

```cmd
php artisan migrate
```

---

## 3) Buka event dan periksa instruksi

1. Dari **Daftar Pelatihan**, klik **Lihat** pada kartu event yang diinginkan.
2. Halaman detail event menampilkan tiga blok fase: **Persiapan**, **Pelaksanaan**, **Pelaporan**. Setiap blok menunjukkan:
   - Rentang tanggal untuk fase tersebut (misal: `preparation_date → start_date`)
   - Progress bar kecil dan hitungan `checked / total`.
3. Daftar instruksi:
   - Instruksi diberi nomor (No.) untuk memudahkan urutan.
   - Gunakan filter fase untuk menampilkan instruksi khusus fase (Persiapan / Pelaksanaan / Pelaporan) atau `All`.
   - Tandai instruksi selesai (checked) menggunakan kontrol yang tersedia.
4. Mengklik blok fase biasanya juga berfungsi sebagai filter cepat untuk menampilkan instruksi fase tersebut.

Catatan: nilai learning model muncul sebagai badge berwarna. Nilai `classical` ditampilkan sebagai **Klasikal**.

---

## 4) Mengunggah dokumen event (Kelengkapan Dokumen)

1. Dari halaman event, klik tautan `Kelengkapan Dokumen` untuk event yang bersangkutan.
2. Halaman menampilkan jenis-jenis dokumen (baris). Untuk tiap baris tersedia dua aksi utama:
   - **Lihat File** — menampilkan view read-only yang berisi:
     1. Link Dokumen (anchor yang dapat diklik atau `-` jika tidak ada)
     2. Lampiran — daftar file terlampir dengan tombol **Download** berwarna biru
     3. Catatan — catatan yang ditampilkan read-only
   - **Edit** — membuka baris edit inline untuk mengubah link, menambahkan file, dan mengedit catatan.

3. Alur edit dan unggah (penting):
   - Klik **Pilih file** untuk memilih file dari perangkat Anda. File yang dipilih ditandai (staged) di sisi klien dan muncul di daftar lampiran dengan tombol **Batal**.
   - File TIDAK langsung diunggah. File akan tetap staged sampai Anda menekan **Simpan**.
   - Tekan **Simpan** untuk mengunggah semua file staged dan menyimpan link/catatan. Jika berhasil, baris edit akan menutup dan lampiran akan muncul di baris dokumen.

4. Download / hapus:
   - Pada panel **Lihat File**, gunakan tombol **Download** untuk mengunduh lampiran.
   - Hapus lampiran tersedia saat mode edit (tombol `Hapus` muncul saat mengedit). Penghapusan memerlukan konfirmasi dan kemudian menghapus file dari server.

Poin penting:
- Staged upload memungkinkan memilih beberapa file dan membatalkannya sebelum menyimpan.
- `Lihat File` murni read-only.

---

## 5) Konvensi UI & warna

- Tombol Download: biru dengan teks putih.
- Progress bar fase menjadi hijau pada 100%.
- Badge learning model berwarna dan dilokalkan.
- Situs menggunakan background batik yang lembut dan kartu transparan untuk menjaga keterbacaan.

---

## 6) Pemecahan masalah (Troubleshooting)

- Gagal unggah (`Network error`):
  - Pastikan endpoint server ada: `POST /documents/{id}/files` dan `PATCH /documents/{id}`.
  - Periksa batas unggah PHP/Laravel (`upload_max_filesize`, `post_max_size`) dan log server.
- Masalah toggle JS (Lihat File / Edit tidak terbuka):
  - Periksa konsol browser untuk error JS dan pastikan skrip aplikasi dimuat.
- Tanggal bantu event hilang (`preparation_date`/`report_date`):
  - Jalankan migrasi: `php artisan migrate` (backup DB terlebih dahulu).

---

## 7) Daftar pemeriksaan cepat untuk event baru

- [ ] Masuk (Masuk Panitia)
- [ ] Buat event (Pelatihan)
- [ ] Buka event → periksa fase dan hitungan instruksi
- [ ] Buka `Kelengkapan Dokumen` → Edit dokumen → `Pilih file` → pastikan file staged muncul → tekan `Simpan`
- [ ] Periksa lampiran di `Lihat File` dan unduh untuk memastikan

---

## 8) Ingin format lain?

Jika Anda menginginkan file ini sebagai dokumen Word `.docx` atau PDF, beri tahu saya format mana yang Anda inginkan dan saya bisa menambahkannya ke repo (mis. di folder `docs/`).

---

Dibuat pada: 2025-11-03
