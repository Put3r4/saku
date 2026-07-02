# Student Flow Documentation

## Alur Mahasiswa - Input Budget sampai Rekomendasi

### 📋 Overview
Dokumen ini menjelaskan alur lengkap mahasiswa dari input budget hingga menerima rekomendasi menu menggunakan algoritma SAW.

---

## 🎯 Fitur yang Sudah Diimplementasi

### 1. **BudgetConstraintRequest** 
**File:** `app/Http/Requests/BudgetConstraintRequest.php`

Validasi input budget dengan aturan:
- ✅ Required: Budget harus diisi
- ✅ Numeric: Harus berupa angka
- ✅ Min: Rp 1.000 (nilai wajar untuk uang makan minimal)
- ✅ Max: Rp 1.000.000 (batas atas untuk mencegah input tidak wajar)

**Custom Error Messages:**
- Pesan error dalam Bahasa Indonesia
- User-friendly dan informatif

---

### 2. **Student DashboardController**
**File:** `app/Http/Controllers/Student/DashboardController.php`

#### Method `index()`
- Menampilkan dashboard mahasiswa
- Form input budget dengan UI yang clean
- Info cards tentang fitur sistem

#### Method `recommend(BudgetConstraintRequest $request, SAWServiceInterface $sawService)`
- Menerima dan memvalidasi input budget
- Memanggil `$sawService->getRecommendations($budget)`
- Membatasi hasil maksimal 5 rekomendasi teratas
- Menampilkan hasil ke halaman rekomendasi

---

### 3. **Routes**
**File:** `routes/web.php`

```php
Route::middleware('auth')->prefix('mahasiswa')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::post('/recommend', [StudentDashboardController::class, 'recommend'])->name('recommend');
    
    // TODO: Sesi 7 - Menu selection
    Route::post('/pilih-menu', ...)->name('select-menu');
});
```

**Perubahan dari placeholder:**
- ✅ Mengganti closure string placeholder dengan controller proper (fix BUG-004)
- ✅ Menambahkan middleware `auth` (hanya user yang login)
- ✅ Tidak menggunakan `role:admin` karena ini untuk mahasiswa
- ✅ Route `/pilih-menu` sudah disiapkan untuk Sesi 7

---

### 4. **Views - Layout Mahasiswa**
**File:** `resources/views/layouts/student.blade.php`

**Perbedaan dengan Layout Admin:**
- ❌ Tidak ada sidebar (lebih simpel)
- ✅ Top navigation bar dengan branding SAKU
- ✅ User info di kanan atas
- ✅ Link logout dan kembali ke home
- ✅ Flash message support (success/error)
- ✅ Footer dengan copyright

**Design Pattern:**
- Clean, modern, mobile-responsive
- Konsisten dengan warna brand SAKU
- Fokus pada user experience mahasiswa

---

### 5. **View - Dashboard Mahasiswa**
**File:** `resources/views/student/dashboard.blade.php`

**Komponen:**

#### Hero Section
- Welcome message dengan nama user
- Penjelasan singkat sistem

#### Form Input Budget
- Input field dengan prefix "Rp"
- Placeholder contoh nilai
- JavaScript untuk format currency (opsional, saat ini plain number)
- Validasi error display
- Helper text (info min/max budget)

#### Info Cards (3 cards)
1. 🍜 **Menu Beragam** - Berbagai pilihan dari vendor
2. 📊 **Metode SAW** - Rekomendasi berbasis algoritma ilmiah
3. 💰 **Sesuai Budget** - Hanya menu yang sesuai anggaran

#### JavaScript
```javascript
function formatCurrency(input) {
    // Remove non-numeric characters
    let value = input.value.replace(/\D/g, '');
    if (value) {
        input.value = value;
    }
}
```
*Catatan: Saat ini simpel, bisa dikembangkan dengan pemisah ribuan jika diperlukan*

---

### 6. **View - Halaman Rekomendasi**
**File:** `resources/views/student/recommendation.blade.php`

#### Header Section
- Tombol "Kembali ke Dashboard"
- Menampilkan budget yang diinput user

#### Case: Ada Rekomendasi
Untuk setiap menu (max 5), tampilkan **card** dengan:

**Rank Badge:**
- Nomor urutan 1-5
- Ranking 1 dengan warna khusus (yellow/gold)
- Badge "⭐ REKOMENDASI TERBAIK" untuk rank 1

**Menu Info:**
- Nama menu (bold, besar)
- Vendor dengan icon
- Harga dengan format Rupiah dan warna hijau
- Skor SAW (4 desimal) dengan background cream

**Action Button:**
- "✓ Pilih Menu Ini"
- Form POST ke `/mahasiswa/pilih-menu`
- Hidden fields: menu_id, budget

**Additional Info (rank 1 only):**
- Penjelasan singkat tentang kriteria SAW

#### Case: Tidak Ada Rekomendasi
- Icon sad face 😔
- Pesan: "Tidak ada menu yang sesuai dengan budget Anda"
- Saran: Naikkan budget atau hubungi admin
- Tombol "Coba Budget Lain" → kembali ke dashboard

#### Bottom Action
- Option untuk mengubah budget
- Tombol "Ubah Budget"

---

## 🔄 User Flow

```
1. Login sebagai mahasiswa (mahasiswa@saku.test / mahasiswa123)
   ↓
2. Redirect ke /mahasiswa/dashboard
   ↓
3. Input budget (contoh: 25000)
   ↓
4. Klik "🔍 Cari Rekomendasi Menu"
   ↓
5. POST ke /mahasiswa/recommend
   ↓
6. SAWCalculationService memproses:
   - Filter menu dengan harga ≤ budget
   - Hitung normalisasi untuk setiap kriteria
   - Hitung skor SAW (Vi) untuk setiap menu
   - Urutkan dari skor tertinggi
   ↓
7. Tampilkan di /mahasiswa/recommendation
   - Top 5 menu dengan skor tertinggi
   - Detail lengkap setiap menu
   - Option "Pilih Menu Ini"
```

---

## 🧪 Testing Manual

### Skenario 1: Budget Normal (Ada Hasil)
```
Input: Rp 25.000
Expected: Menampilkan 5 menu teratas yang harganya ≤ 25.000
Verify:
- ✅ Menu diurutkan dari skor SAW tertinggi
- ✅ Rank 1 memiliki badge "REKOMENDASI TERBAIK"
- ✅ Harga semua menu ≤ 25.000
- ✅ Skor SAW ditampilkan dengan 4 desimal
```

### Skenario 2: Budget Rendah (Tidak Ada Hasil)
```
Input: Rp 5.000
Expected: Pesan "Tidak ada menu yang sesuai"
Verify:
- ✅ Pesan error muncul dengan jelas
- ✅ Tombol "Coba Budget Lain" berfungsi
- ✅ Tidak ada error/crash
```

### Skenario 3: Budget Tinggi (Banyak Hasil)
```
Input: Rp 100.000
Expected: Tetap hanya menampilkan 5 menu teratas
Verify:
- ✅ Maksimal 5 menu ditampilkan
- ✅ Menu adalah 5 terbaik (skor tertinggi)
```

### Skenario 4: Validasi Error
```
Input: (kosong) atau "abc" atau -1000
Expected: Error validasi muncul
Verify:
- ✅ "Budget harus diisi"
- ✅ "Budget harus berupa angka"
- ✅ "Budget minimal Rp 1.000"
```

---

## 📦 Files Created/Modified

### Created:
1. ✅ `app/Http/Requests/BudgetConstraintRequest.php`
2. ✅ `app/Http/Controllers/Student/DashboardController.php`
3. ✅ `resources/views/layouts/student.blade.php`
4. ✅ `resources/views/student/dashboard.blade.php`
5. ✅ `resources/views/student/recommendation.blade.php`

### Modified:
1. ✅ `routes/web.php` - Added student routes, fixed BUG-004

---

## 🚀 Next Steps (Sesi 7)

### Fitur "Pilih Menu Ini"
Akan diimplementasi:
1. Controller method untuk handle pemilihan menu
2. Simpan history pemilihan ke database (tabel `selections` atau `history`)
3. Halaman riwayat pemilihan mahasiswa
4. Statistik penggunaan untuk admin

**Endpoint yang sudah disiapkan:**
```php
Route::post('/pilih-menu', ...)->name('student.select-menu');
```

---

## 🎨 Design Highlights

### Color Scheme (SAKU Brand)
- **Navy (#2A324A)**: Teks utama
- **Steel (#7A82A6)**: Teks sekunder
- **Cream (#E1DBCB)**: Background aksen
- **Orange (#D68438)**: Button primary / CTA
- **Rust (#8D4F37)**: Button hover

### UI/UX Principles
- ✅ Clean & modern design
- ✅ Mobile responsive
- ✅ Consistent with admin layout (brand consistency)
- ✅ Clear call-to-action buttons
- ✅ Informative feedback (success/error messages)
- ✅ User-friendly error handling

---

## 🔧 Technical Notes

### Dependency Injection
```php
public function recommend(
    BudgetConstraintRequest $request, 
    SAWServiceInterface $sawService
)
```
- ✅ Menggunakan contract/interface untuk loose coupling
- ✅ Service sudah di-bind di `AppServiceProvider`
- ✅ Testable dan maintainable

### Data Flow
```
Request → Validation → Controller → Service → View
```

### Session Management
Saat ini hasil recommendation langsung dikirim ke view, tidak disimpan di session.
Jika diperlukan untuk history, bisa ditambahkan di Sesi 7.

---

## 📝 Catatan Implementasi

### BUG-004 Fixed ✅
Route placeholder di `/mahasiswa/dashboard` yang sebelumnya berupa:
```php
Route::get('/dashboard', function () {
    return "Area Mahasiswa: Form Input Anggaran & Hasil Rekomendasi";
});
```

Sudah diganti dengan proper controller:
```php
Route::get('/dashboard', [StudentDashboardController::class, 'index']);
```

### Security
- ✅ Middleware `auth` untuk memastikan user sudah login
- ✅ CSRF protection di semua form POST
- ✅ Input validation dengan FormRequest
- ✅ No SQL injection (menggunakan Eloquent ORM)

### Performance
- ✅ Limit hasil maksimal 5 (tidak perlu load semua menu)
- ✅ Eager loading di service (jika ada relasi)
- ✅ Efficient calculation di SAWCalculationService

---

## ✅ Testing Checklist

Sebelum deploy, pastikan:
- [ ] Login sebagai mahasiswa berhasil
- [ ] Dashboard mahasiswa tampil dengan form
- [ ] Input budget normal → hasil muncul
- [ ] Input budget rendah → pesan "tidak ada menu"
- [ ] Validasi error muncul untuk input invalid
- [ ] Rank 1 memiliki badge "REKOMENDASI TERBAIK"
- [ ] Semua harga ≤ budget
- [ ] Skor SAW ditampilkan dengan benar
- [ ] Tombol "Kembali" berfungsi
- [ ] Tombol "Pilih Menu Ini" mengarah ke route yang benar (meski belum implement)
- [ ] Mobile responsive
- [ ] Logout berfungsi

---

**Status:** ✅ Ready for Testing
**Next:** Sesi 7 - Implement menu selection & history
