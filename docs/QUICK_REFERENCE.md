# 🚀 QUICK REFERENCE - SAKU Project

**One-Page Cheat Sheet for New Developers**  
**Date:** 01 Juli 2026

---

## 📊 Project Status at a Glance

```
Progress: ███████░░░░░░░░░░░░░░ 25%

✅ DONE:
  - Authentication & Authorization (Fortify + 2FA)
  - Admin Dashboard UI (static data)
  - Criteria CRUD (create only, edit/delete missing)
  - Landing Page
  - User Management

🔴 MISSING (CRITICAL):
  - SAW Engine (0%)
  - Menu Management (0%)
  - Matrix Input (0%)
  - Student Dashboard (0%)
  - Recommendation Display (0%)
  - Budget History (0%)
```

---

## 🎯 What is SAKU?

**Purpose:** Help mahasiswa choose daily menu based on budget using SAW algorithm

**How it Works:**
1. Student input budget (e.g. Rp 20,000)
2. System filter menus <= budget
3. System run SAW algorithm (rank by: nutrition, distance, taste, etc)
4. System show top 5 recommendations
5. Student pick menu
6. System log history

**Core Algorithm:** Simple Additive Weighting (SAW)
- Formula: `Vi = Σ(wj × rij)`
- Normalization: Benefit (max) vs Cost (min)

---

## 🗄️ Database Schema (Current + Missing)

**Existing Tables:**
```sql
users (id, name, email, password, role)            ✅
criteria (id, kode, nama_kriteria, tipe, bobot)   ✅
cache, jobs, sessions                              ✅
```

**MISSING Tables (MUST CREATE):**
```sql
menus (id, vendor_name, menu_name, price, ...)    ❌ CRITICAL
menu_evaluations (id, menu_id, criterion_id, value) ❌ CRITICAL
budget_histories (id, user_id, budget_amount, ...)  ❌ IMPORTANT
```

---

## 🛤️ Routes Quick Map

```php
// Public
GET  /                   # Landing page

// Auth
GET  /masuk             # Login
POST /keluar            # Logout

// Admin (middleware: auth, role:admin)
GET  /admin/dashboard             # Dashboard
GET  /admin/criteria              # List criteria
POST /admin/criteria              # Store criteria
❌  /admin/criteria/{id}/edit    # MISSING
❌  /admin/menu/*                # MISSING - all CRUD
❌  /admin/matrix                # MISSING - input matrix

// Student (middleware: auth)
❌  /mahasiswa/dashboard         # PLACEHOLDER (string only)
❌  /mahasiswa/recommend         # MISSING
❌  /mahasiswa/history           # MISSING
```

---

## 📁 Key Files Location

**Models:**
```
app/Models/User.php              ✅
app/Models/Criterion.php         ✅
app/Models/Menu.php              ❌ CREATE THIS
app/Models/MenuEvaluation.php    ❌ CREATE THIS
app/Models/BudgetHistory.php     ❌ CREATE THIS
```

**Controllers:**
```
app/Http/Controllers/AuthController.php                    ✅
app/Http/Controllers/Admin/CriterionController.php         ✅ (90%)
app/Http/Controllers/Admin/MenuController.php              ❌ CREATE THIS
app/Http/Controllers/Admin/MatrixController.php            ❌ CREATE THIS
app/Http/Controllers/Student/DashboardController.php       ❌ CREATE THIS
```

**Services (CORE):**
```
app/Contracts/SAWServiceInterface.php         ❌ CREATE THIS
app/Services/SAWCalculationService.php        ❌ CREATE THIS
```

**Views:**
```
resources/views/admin/dashboard.blade.php              ✅
resources/views/admin/criteria/{index,create}.blade.php ✅
resources/views/admin/menu/*                           ❌ CREATE
resources/views/admin/matrix/index.blade.php           ❌ CREATE
resources/views/student/dashboard.blade.php            ❌ CREATE
resources/views/student/recommendation.blade.php       ❌ CREATE
```

---

## 🔑 Login Credentials

```
Admin:
  Email: admin@saku.test
  Pass:  admin123
  
Student:
  Email: mahasiswa@saku.test
  Pass:  mahasiswa123
```

---

## ⚡ Essential Commands

**Setup:**
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
```

**Development:**
```bash
composer dev                    # Run all (server + queue + vite)
# OR manually:
php artisan serve              # Backend
npm run dev                    # Frontend
php artisan queue:work         # Queue (optional)
```

**Database:**
```bash
php artisan migrate:fresh --seed    # Reset DB
php artisan tinker                   # Interactive shell
```

**Testing:**
```bash
php artisan test                # Run Pest tests
php artisan test --coverage     # With coverage
php artisan test --filter=SAW   # Specific test
```

**Code Quality:**
```bash
composer lint                   # Laravel Pint
npm run lint                    # ESLint
npm run format                  # Prettier
```

---

## 🐛 Known Bugs (Fix These First)

1. **BUG-001:** Total criteria weight not validated (can exceed 1.00)
2. **BUG-002:** Edit/Delete criteria buttons not functional
3. **BUG-003:** Admin dashboard shows hardcoded 0
4. **BUG-004:** Student dashboard returns string placeholder

---

## 📚 Must-Read Docs (Priority Order)

1. `docs/REKAP.md` - **START HERE** (complete audit)
2. `docs/NEXT_TASKS.md` - What to do next
3. `docs/SAW_ENGINE_ANALYSIS.md` - Algorithm guide
4. `docs/PROJECT_STRUCTURE.md` - File organization
5. `docs/BUG_REPORT.md` - Bug details

---

## ⏱️ Time Estimates

| Task | Time | Priority |
|------|------|----------|
| Create missing models & migrations | 2h | P0 |
| Create seeders | 1h | P0 |
| Build SAW Service | 6h | P0 CRITICAL |
| Write SAW tests | 4h | P0 CRITICAL |
| Build Menu CRUD (admin) | 4h | P0 |
| Build Matrix Input (admin) | 6h | P0 |
| Build Student Dashboard | 3h | P0 |
| Build Recommendation Display | 4h | P0 |
| Fix known bugs | 2h | P1 |
| Polish & testing | 6h | P1 |
| **TOTAL** | **~38h** | **~5 days** |

---

## 🎯 MVP Definition of Done

MVP is complete when:
- [x] ~~Authentication works~~ ✅
- [ ] Admin can manage criteria ✅ (90%, missing edit/delete)
- [ ] Admin can manage menus ❌
- [ ] Admin can input matrix values ❌
- [ ] Student can input budget ❌
- [ ] System runs SAW algorithm ❌ **CRITICAL**
- [ ] System shows ranked recommendations ❌
- [ ] Student can select menu ❌
- [ ] System logs budget history ❌
- [ ] All tests passing ❌
- [ ] Code coverage >80% ❌

**Current MVP Completion: 10%**

---

## 💡 Pro Tips

1. **Don't skip Week 1** (database setup) - everything depends on it
2. **Test SAW engine thoroughly** - math errors are costly
3. **Use tinker often** - `php artisan tinker` to test relationships
4. **Commit frequently** - small commits with clear messages
5. **Follow existing patterns** - match the UI style already there
6. **Read Laravel 13 docs** - it's different from older versions
7. **Budget is NOT a criterion** - it's a pre-filter (WHERE clause)

---

## 🚨 Critical Path (Can't Skip)

```
Week 1: Database
   ↓
Week 2: SAW Engine + Tests
   ↓
Week 2-3: Admin CRUD
   ↓
Week 3: Student Features
   ↓
Week 4: Polish & Deploy
```

Skip any week = project fails.

---

## 📞 Emergency Contacts

- **Technical Lead:** (your contact)
- **Documentation:** `docs/` folder
- **Slack/Discord:** (if any)
- **Repository:** (git URL)

---

**Last Updated:** 01 Juli 2026  
**Audit By:** Senior Software Architect

**Good luck! You got this! 🚀**
