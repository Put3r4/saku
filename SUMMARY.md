# 📋 Ringkasan Sistem Pendukung Keputusan Menu (SAKU)

## 🎯 Deskripsi Proyek

**SAKU** (Sistem Aplikasi untuk Keputusan Utama) adalah platform web berbasis Laravel 13 yang dirancang untuk membantu mahasiswa memilih menu makanan harian berdasarkan ketersediaan anggaran dan kriteria nutrisi. Sistem ini menggunakan algoritma **Simple Additive Weighting (SAW)** untuk memberikan rekomendasi menu yang optimal.

---

## 🏗️ Arsitektur Sistem

### Stack Teknologi
- **Framework**: Laravel 13.x
- **PHP**: >= 8.3
- **Database**: MySQL/MariaDB
- **Frontend**: Blade Templates + TALL Stack (Tailwind CSS, Alpine.js, Livewire, Laravel)
- **Autentikasi**: Laravel Fortify + Passkey Support

### Pola Arsitektur
- **MVC Pattern** dengan **Service Layer** untuk business logic
- **Repository Pattern** via Eloquent ORM
- **Dependency Injection** untuk SAW calculation service
- **Interface-based** programming (SAWServiceInterface)

---

## 📊 Struktur Database

### Tabel Utama

#### 1. **users**
Menyimpan data pengguna sistem (admin dan mahasiswa)
```sql
- id: bigint (PK)
- name: string
- email: string (unique)
- password: string
- role: enum('admin', 'mahasiswa')
- passkey_id: string (untuk autentikasi passkey)
- created_at, updated_at
```

#### 2. **criteria**
Menyimpan kriteria evaluasi untuk algoritma SAW
```sql
- id: bigint (PK)
- kode: string (unique) -- C1, C2, C3, dst
- nama_kriteria: string -- "Kandungan Kalori", "Jarak Lokasi"
- tipe: enum('benefit', 'cost')
- bobot: decimal(5,2) -- Total harus = 1.00
- created_at, updated_at
```

**Contoh Kriteria:**
- C1: Persentase Kepadatan Nutrisi Kalorik (Benefit)
- C2: Indeks Keragaman Variasi Menu (Benefit)
- C3: Jarak Aksesibilitas Lokasi (Cost)
- C4: Waktu Degradasi Ketahanan Pangan (Cost)

#### 3. **menus**
Menyimpan data menu makanan dari berbagai vendor
```sql
- id: bigint (PK)
- vendor_name: string
- menu_name: string
- price: decimal(10,2)
- description: text (nullable)
- image_url: string (nullable)
- is_available: boolean (default: true)
- created_at, updated_at
- INDEX: price, is_available
```

#### 4. **menu_evaluations**
Tabel pivot yang menyimpan nilai evaluasi menu terhadap setiap kriteria (Matriks Keputusan)
```sql
- id: bigint (PK)
- menu_id: FK → menus (cascade delete)
- criterion_id: FK → criteria (cascade delete)
- value: decimal(10,2)
- UNIQUE: (menu_id, criterion_id)
```

#### 5. **budget_histories**
Menyimpan riwayat rekomendasi dan pilihan menu mahasiswa
```sql
- id: bigint (PK)
- user_id: FK → users (cascade delete)
- budget_amount: decimal(10,2)
- selected_menu_id: FK → menus (null on delete)
- recommendation_data: json -- Top 5 rekomendasi
- created_at
- INDEX: (user_id, created_at)
```

---

## 🔐 Sistem Autentikasi

### Fitur Autentikasi
- **Laravel Fortify** untuk foundational authentication
- **Passkey Support** untuk autentikasi modern berbasis biometrik
- **Role-Based Access Control (RBAC)**: Admin vs Mahasiswa
- **Session-based Authentication**

### Middleware
- `auth` - Memastikan pengguna sudah login
- `role:admin` - Hanya admin yang bisa akses
- `role:mahasiswa` - Hanya mahasiswa yang bisa akses
- `guest` - Hanya untuk pengguna belum login

---

## 🎭 Role & Permissions

### 1. Administrator
**Akses:** `/admin/*`

**Fitur:**
- ✅ Dashboard statistik sistem
- ✅ **Manajemen Kriteria** (CRUD)
  - Tambah/edit/hapus kriteria
  - Validasi total bobot = 1.00
  - Set tipe: benefit/cost
- ✅ **Manajemen Menu** (CRUD)
  - Tambah/edit/hapus menu makanan
  - Set harga dan ketersediaan
  - Upload gambar (URL)
- ✅ **Input Matriks Evaluasi**
  - Grid input: menu × kriteria
  - Validasi nilai numerik
  - Bulk update
- ✅ **Monitoring Aktivitas**
  - Lihat riwayat rekomendasi semua mahasiswa
  - Statistik penggunaan sistem

### 2. Mahasiswa
**Akses:** `/mahasiswa/*`

**Fitur:**
- ✅ Dashboard input anggaran
- ✅ **Sistem Rekomendasi**
  - Input budget maksimal
  - Lihat top 5 rekomendasi menu
  - SAW score untuk setiap menu
- ✅ **Pilih Menu**
  - Simpan pilihan ke riwayat
  - Tracking pengeluaran
- ✅ **Riwayat Rekomendasi**
  - Lihat histori pilihan menu
  - Data rekomendasi tersimpan
  - Analisis pengeluaran

---

## 🧮 Algoritma SAW (Simple Additive Weighting)

### Alur Proses Perhitungan

#### Fase 1: Pre-filtering (Budget Constraint)
```php
// BUKAN bagian dari kriteria SAW, tetapi HARD CONSTRAINT
$menus = Menu::where('price', '<=', $budget)
            ->where('is_available', true)
            ->get();
```

**Alasan:** Memfilter di level database lebih efisien daripada memproses semua menu lalu dibuang.

#### Fase 2: Build Decision Matrix (X)
Matriks mentah: `X[i][j]` = nilai menu `i` pada kriteria `j`

```
      C1   C2   C3   C4
M1   450   4   1.5   30
M2   380   3   2.0   45
M3   520   5   0.8   25
```

#### Fase 3: Normalisasi Matrix (R)
**Benefit Criteria:** `r[i][j] = x[i][j] / max(x[j])`
**Cost Criteria:** `r[i][j] = min(x[j]) / x[i][j]`

```php
// Contoh: C1 = Benefit (kalori), max = 520
r[1][1] = 450 / 520 = 0.8654

// Contoh: C3 = Cost (jarak), min = 0.8
r[1][3] = 0.8 / 1.5 = 0.5333
```

#### Fase 4: Weighted Score (V)
`V[i] = Σ(w[j] × r[i][j])`

```php
// Bobot: [0.4, 0.3, 0.2, 0.1]
V[1] = (0.4 × 0.8654) + (0.3 × 0.8) + (0.2 × 0.5333) + (0.1 × 0.8333)
     = 0.7363
```

#### Fase 5: Ranking
Menu diurutkan berdasarkan skor tertinggi → terendah.

### Edge Cases Handling
- ✅ Tidak ada menu dalam budget → return empty collection
- ✅ Kriteria belum dikonfigurasi → throw exception
- ✅ Semua nilai kolom = 0 → normalisasi = 0 (avoid division by zero)
- ✅ Cost criteria dengan nilai 0 → diabaikan (tidak valid)

---

## 🎯 Fitur-Fitur Utama

### 1. Dashboard Admin
**Controller:** `Admin\DashboardController`
**Route:** `GET /admin/dashboard`

**Statistik:**
- Total menu yang tersedia
- Total kriteria terkonfigurasi
- Total bobot kriteria (validasi = 1.00)
- Total mahasiswa terdaftar
- Total rekomendasi yang dihasilkan

**Aktivitas Terbaru:**
- 5 rekomendasi terakhir
- Menampilkan: nama user, menu terpilih, budget, SAW score

### 2. Manajemen Kriteria
**Controller:** `Admin\CriterionController`
**Routes:**
- `GET /admin/criteria` - List kriteria
- `GET /admin/criteria/create` - Form tambah
- `POST /admin/criteria` - Simpan kriteria baru
- `GET /admin/criteria/{id}/edit` - Form edit
- `PUT /admin/criteria/{id}` - Update kriteria
- `DELETE /admin/criteria/{id}` - Hapus kriteria

**Validasi Khusus:**
- ✅ Kode kriteria harus unique (C1, C2, dst)
- ✅ Total bobot tidak boleh > 1.00
- ✅ Bobot minimal 0.01, maksimal 1.00
- ✅ Tipe hanya: 'benefit' atau 'cost'

**Contoh Error Message:**
```
Total bobot akan melebihi 1.00! 
Total saat ini: 0.7000, bobot yang diinput: 0.4000, 
total akan menjadi: 1.1000. 
Maksimal bobot yang bisa diinput: 0.3000
```

### 3. Manajemen Menu
**Controller:** `Admin\MenuController`
**Routes:**
- `GET /admin/menu` - List menu
- `GET /admin/menu/create` - Form tambah
- `POST /admin/menu` - Simpan menu baru
- `GET /admin/menu/{id}/edit` - Form edit
- `PUT /admin/menu/{id}` - Update menu
- `DELETE /admin/menu/{id}` - Hapus menu

**Fitur:**
- ✅ Input vendor dan nama menu
- ✅ Set harga dengan validasi numerik
- ✅ Deskripsi opsional
- ✅ URL gambar opsional
- ✅ Toggle ketersediaan (is_available)
- ✅ Auto-delete evaluasi terkait saat hapus menu

### 4. Input Matriks Evaluasi
**Controller:** `Admin\MatrixController`
**Routes:**
- `GET /admin/matrix` - Grid input matriks
- `POST /admin/matrix` - Bulk update nilai evaluasi

**Fitur:**
- ✅ Grid dinamis: menu (baris) × kriteria (kolom)
- ✅ Input numerik untuk setiap cell
- ✅ Validasi: semua nilai harus diisi, numerik, >= 0
- ✅ Transaction-safe bulk update
- ✅ Auto-create atau update nilai existing

**Validasi:**
```php
'values.*.*' => 'required|numeric|min:0'
```

### 5. Dashboard Mahasiswa
**Controller:** `Student\DashboardController`
**Route:** `GET /mahasiswa/dashboard`

**Fitur:**
- ✅ Form input budget maksimal
- ✅ Validasi numerik positif
- ✅ Submit untuk mendapat rekomendasi

### 6. Sistem Rekomendasi
**Controller:** `Student\DashboardController@recommend`
**Route:** `POST /mahasiswa/recommend`
**Request:** `BudgetConstraintRequest`

**Proses:**
1. Validasi input budget
2. Panggil `SAWCalculationService::getRecommendations($budget)`
3. Ambil top 5 menu dengan skor tertinggi
4. Simpan ke session (untuk tracking pilihan nanti)
5. Tampilkan hasil dengan:
   - Nama menu & vendor
   - Harga
   - SAW Score (4 desimal)
   - Ranking

**Output:**
```json
[
  {
    "menu_id": 1,
    "menu_name": "Nasi Goreng Spesial",
    "vendor": "Warung Bu Siti",
    "price": 15000.00,
    "saw_score": 0.8654,
    "rank": 1
  },
  ...
]
```

### 7. Pilih Menu
**Controller:** `Student\DashboardController@selectMenu`
**Route:** `POST /mahasiswa/pilih-menu`

**Proses:**
1. Validasi menu_id dan budget
2. Ambil data rekomendasi dari session
3. Simpan ke `budget_histories`:
   - user_id
   - budget_amount
   - selected_menu_id
   - recommendation_data (JSON top 5)
4. Clear session
5. Redirect ke riwayat

### 8. Riwayat Rekomendasi
**Controller:** `Student\HistoryController`
**Route:** `GET /mahasiswa/riwayat`

**Fitur:**
- ✅ List semua riwayat mahasiswa (latest first)
- ✅ Detail setiap record:
  - Tanggal & waktu
  - Budget yang diinput
  - Menu yang dipilih
  - Top 5 rekomendasi (JSON)
  - SAW score
- ✅ Filter dan pencarian (opsional)

---

## 🔧 Service Layer

### SAWCalculationService
**Interface:** `App\Contracts\SAWServiceInterface`
**Implementation:** `App\Services\SAWCalculationService`

**Method Utama:**
```php
public function getRecommendations(float $budget): Collection
```

**Dependencies:** Tidak ada (pure calculation service)

**Method Pembantu:**
1. `filterMenusByBudget(float $budget): Collection`
   - Filter menu dengan `price <= budget`
   - Eager load evaluations + criteria

2. `buildDecisionMatrix(Collection $menus, Collection $criteria): array`
   - Build 2D array [menu_index][criterion_index]
   - Default value = 0 jika evaluasi tidak ada

3. `normalizeMatrix(array $matrix, Collection $criteria): array`
   - Normalisasi berdasarkan tipe kriteria
   - Handle edge cases (semua nilai 0, division by zero)

4. `calculateWeightedScore(array $normalizedMatrix, array $weights): array`
   - Kalikan dengan bobot dan jumlahkan
   - Round ke 4 desimal

5. `rankAlternatives(array $scores, Collection $menus): Collection`
   - Sort descending by score
   - Assign ranking (1, 2, 3, ...)

**Dependency Injection:**
```php
// bootstrap/providers.php
$this->app->bind(
    SAWServiceInterface::class,
    SAWCalculationService::class
);

// Di controller
public function recommend(
    BudgetConstraintRequest $request, 
    SAWServiceInterface $sawService
) {
    $recommendations = $sawService->getRecommendations($budget);
}
```

---

## 🛣️ Routing Structure

### Publik Routes
```php
GET  /                  → HomeController@index
GET  /tentang-saw       → HomeController@tentangSaw
GET  /daftar-menu       → HomeController@daftarMenu
GET  /masuk            → AuthController@index
POST /masuk            → AuthController@authenticate
POST /keluar           → AuthController@logout
```

### Admin Routes (Prefix: `/admin`)
```php
GET  /admin/dashboard           → AdminDashboardController@index
GET  /admin/criteria            → CriterionController@index
POST /admin/criteria            → CriterionController@store
GET  /admin/criteria/{id}/edit  → CriterionController@edit
PUT  /admin/criteria/{id}       → CriterionController@update
DELETE /admin/criteria/{id}     → CriterionController@destroy

GET  /admin/menu            → MenuController@index
POST /admin/menu            → MenuController@store
GET  /admin/menu/{id}/edit  → MenuController@edit
PUT  /admin/menu/{id}       → MenuController@update
DELETE /admin/menu/{id}     → MenuController@destroy

GET  /admin/matrix          → MatrixController@index
POST /admin/matrix          → MatrixController@update
```

### Mahasiswa Routes (Prefix: `/mahasiswa`)
```php
GET  /mahasiswa/dashboard    → StudentDashboardController@index
POST /mahasiswa/recommend    → StudentDashboardController@recommend
POST /mahasiswa/pilih-menu   → StudentDashboardController@selectMenu
GET  /mahasiswa/riwayat      → HistoryController@index
```

---

## 📝 Request Validation

### BudgetConstraintRequest
**File:** `app/Http/Requests/BudgetConstraintRequest.php`

```php
public function rules(): array
{
    return [
        'budget' => [
            'required',
            'numeric',
            'min:1000',
            'max:1000000'
        ],
    ];
}

public function messages(): array
{
    return [
        'budget.required' => 'Budget wajib diisi.',
        'budget.numeric' => 'Budget harus berupa angka.',
        'budget.min' => 'Budget minimal Rp 1.000.',
        'budget.max' => 'Budget maksimal Rp 1.000.000.',
    ];
}
```

---

## 🎨 View Structure

### Layout Utama
```
resources/views/
├── layouts/
│   ├── app.blade.php          # Base layout
│   ├── navigation.blade.php    # Top navbar
│   └── sidebar.blade.php       # Admin sidebar
├── admin/
│   ├── dashboard.blade.php
│   ├── criteria/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── menu/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   └── matrix/
│       └── index.blade.php
├── student/
│   ├── dashboard.blade.php
│   ├── recommendation.blade.php
│   └── history/
│       └── index.blade.php
└── auth/
    └── login.blade.php
```

---

## 🧪 Testing Manual

Lihat file: **`TESTING_MANUAL_GUIDE.md`** untuk panduan lengkap testing manual sistem.

**Coverage Testing:**
- ✅ Autentikasi & Role-based access
- ✅ CRUD Kriteria dengan validasi bobot
- ✅ CRUD Menu makanan
- ✅ Input matriks evaluasi
- ✅ Algoritma SAW end-to-end
- ✅ Budget constraint filtering
- ✅ Simpan dan lihat riwayat

---

## 🚀 Setup & Installation

### Prerequisites
```bash
- PHP >= 8.3
- Composer
- MySQL/MariaDB
- Node.js & NPM (untuk frontend assets)
```

### Installation Steps
```bash
# 1. Clone repository
git clone <repo-url>
cd saku

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database di .env
DB_DATABASE=saku
DB_USERNAME=root
DB_PASSWORD=

# 5. Run migrations
php artisan migrate

# 6. (Opsional) Seed data dummy
php artisan db:seed

# 7. Build frontend assets
npm run build

# 8. Serve application
php artisan serve
```

### Akses Aplikasi
```
URL: http://localhost:8000

Admin Login:
Email: admin@saku.test
Password: password

Mahasiswa Login:
Email: student@saku.test
Password: password
```

---

## 📦 Dependencies Utama

### Backend
```json
{
  "laravel/framework": "^13.0",
  "laravel/fortify": "^2.0",
  "laravel/sanctum": "^4.0"
}
```

### Frontend
```json
{
  "tailwindcss": "^3.4",
  "alpinejs": "^3.14",
  "livewire": "^3.0"
}
```

---

## 🔒 Security Features

- ✅ **CSRF Protection** (Laravel default)
- ✅ **SQL Injection Prevention** (Eloquent ORM)
- ✅ **XSS Protection** (Blade auto-escaping)
- ✅ **Password Hashing** (bcrypt)
- ✅ **Passkey Authentication** (Laravel Fortify)
- ✅ **Role-Based Access Control** (Middleware)
- ✅ **Input Validation** (Form Request)
- ✅ **Session Security** (httpOnly, secure cookies)

---

## 📊 Performance Optimization

### Database
- ✅ Indexing pada kolom `price`, `is_available`, `user_id`, `created_at`
- ✅ Eager Loading untuk menghindari N+1 query problem
- ✅ Unique constraint pada tabel pivot
- ✅ Cascade delete untuk referential integrity

### Query Optimization
```php
// ❌ BAD: N+1 problem
$menus = Menu::all();
foreach ($menus as $menu) {
    $menu->evaluations; // Query di dalam loop
}

// ✅ GOOD: Eager loading
$menus = Menu::with('evaluations.criterion')->get();
```

### Caching Strategy
```php
// Cache kriteria (jarang berubah)
$criteria = Cache::remember('criteria', 3600, function () {
    return Criterion::all();
});
```

---

## 🐛 Error Handling

### Exception Handling
```php
// SAWCalculationService
if ($criteria->isEmpty()) {
    throw new \Exception(
        'Kriteria belum dikonfigurasi. Silakan tambahkan kriteria terlebih dahulu.'
    );
}
```

### Validation Errors
```php
// CriterionController
if ($newTotalBobot > 1.0001) {
    throw ValidationException::withMessages([
        'bobot' => sprintf(
            'Total bobot akan melebihi 1.00! Total saat ini: %.4f',
            $currentTotalBobot
        )
    ]);
}
```

---

## 📈 Monitoring & Analytics

### Admin Dashboard Metrics
- Total menu tersedia
- Total kriteria terkonfigurasi
- Validasi total bobot kriteria
- Total mahasiswa terdaftar
- Total rekomendasi dihasilkan
- 5 aktivitas terbaru

### Budget History Analytics
- Tracking pengeluaran mahasiswa
- Pola pemilihan menu
- Distribusi budget
- Menu paling populer

---

## 🔮 Future Enhancements

### Fitur yang Bisa Ditambahkan
- [ ] Export laporan ke PDF/Excel
- [ ] Visualisasi grafik pengeluaran mahasiswa
- [ ] Notifikasi email untuk rekomendasi baru
- [ ] API untuk mobile app
- [ ] Multi-language support
- [ ] Integration dengan sistem pembayaran
- [ ] Recommendation history comparison
- [ ] Advanced filtering (vegetarian, halal, dll)
- [ ] Rating & review menu oleh mahasiswa
- [ ] AI-powered personalized recommendations

---

## 📚 Referensi

### Dokumentasi
- [Laravel 13 Documentation](https://laravel.com/docs/13.x)
- [Simple Additive Weighting Method](https://pdfs.semanticscholar.org/be75/7d01f7f9049346e67fa3ad9c5f6ff9c4df9c.pdf)
- [Laravel Fortify](https://laravel.com/docs/13.x/fortify)
- [Blade Templates](https://laravel.com/docs/13.x/blade)

### Sumber Akademik
1. SISTEM PENDUKUNG KEPUTUSAN - Semantic Scholar
2. Decision Support System Using Simple Additive Weighting Method - MDPI
3. Service Layer in Laravel and PHP - Carlos Santiago

---

## 👥 Tim Pengembang

**Project:** SAKU - Sistem Aplikasi untuk Keputusan Utama  
**Framework:** Laravel 13.x  
**Metode:** Simple Additive Weighting (SAW)  
**Tahun:** 2026  

---

## 📝 Lisensi

Project ini dikembangkan untuk keperluan akademik dan edukasi.

---

## 📞 Kontak & Support

Untuk pertanyaan atau dukungan teknis, silakan hubungi:
- Email: support@saku.test
- GitHub Issues: [repository-url]/issues

---

**Last Updated:** Juli 2, 2026  
**Version:** 1.0.0  
**Status:** ✅ Production Ready
