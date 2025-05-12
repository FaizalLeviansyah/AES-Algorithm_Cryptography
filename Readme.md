
# 📄 Panduan Penggunaan Aplikasi Kriptografi AES

## 🧾 Deskripsi Singkat
Aplikasi web ini digunakan untuk melakukan enkripsi dan dekripsi file menggunakan algoritma AES-128 dan AES-256. Sistem ini juga mencatat waktu proses dan hash integritas data.

---

## 🔐 Enkripsi File

1. Login ke aplikasi.
2. Masuk ke menu **Enkripsi**.
3. Upload file dengan format: `.txt`, `.docx`, `.pptx`, atau `.pdf`
4. Masukkan **Key** (bebas, bisa berupa kata/angka).
5. Masukkan **Deskripsi** file.
6. Pilih **Algoritma**: AES-128 atau AES-256.
7. Klik **Enkripsi File**.
8. File terenkripsi akan disimpan di folder: `dashboard/hasil_ekripsi/`

---

## 🔓 Dekripsi File

1. Masuk ke menu **Dekripsi**.
2. Pilih file dari daftar yang sudah terenkripsi.
3. Masukkan **Key** (harus sama persis dengan saat enkripsi).
4. Klik **Dekripsi File**.
5. File hasil dekripsi akan disimpan di folder: `dashboard/hasil_dekripsi/`

---

## 📂 Struktur Folder Penting

```
AES-Algorithm_Cryptography/
├── dashboard/
│   ├── encrypt-process.php
│   ├── decrypt-process.php
│   ├── hasil_ekripsi/
│   ├── hasil_dekripsi/
```

---

## ⚠️ Catatan Tambahan

- Jika password salah atau file rusak → sistem akan menolak dekripsi.
- File hash SHA-256 dicek untuk menjamin integritas.
- Sistem mencatat waktu proses enkripsi dan dekripsi dalam milidetik.

---

## 👨‍💻 Developer

- Nama: Faizal Leviansyah
- Proyek: Tugas Akhir (Skripsi)
- Judul: *End-to-End Cryptography using AES Algorithms with Comparison of AES-128 & 256 to Secure Confidential Document Archives*

