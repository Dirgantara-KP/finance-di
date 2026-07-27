# Finance DI

Sistem manajemen keuangan untuk Dirgantara. Dibangun dengan Laravel 13, Filament 5, dan integrasi Keycloak untuk autentikasi.

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | PHP 8.3, Laravel 13 |
| Admin Panel | Filament 5 |
| Database | MySQL 8.0 |
| Cache/Queue | Redis 7 |
| Auth Provider | Keycloak 25.0 |
| Frontend | Vite 8, Tailwind CSS 4 |
| Web Server | Nginx (Alpine) |
| Testing | Pest 4 |
| Static Analysis | PHPStan (Larastan) Level 5 |
| Code Style | Laravel Pint |

## Docker Services

| Service | URL | Port |
|---------|-----|------|
| Aplikasi | http://localhost:8080 | 8080 |
| Keycloak Admin | http://localhost:8180 | 8180 |
| phpMyAdmin | http://localhost:8081 | 8081 |
| MySQL | localhost | 3306 |
| Redis | localhost | 6379 |

## Prasyarat

- [Docker](https://docs.docker.com/get-docker/) >= 24.0
- [Docker Compose](https://docs.docker.com/compose/install/) >= 2.20
- [Git](https://git-scm.com/)

## Cara Menjalankan

### 1. Clone repository

```bash
git clone https://github.com/username/finance-di.git
cd finance-di
```

### 2. Setup environment

```bash
cp .env.example .env
```

Buka file `.env` dan isi bagian Docker:

```env
# ─── Docker MySQL ───
MYSQL_ROOT_PASSWORD=your_root_password
MYSQL_DATABASE=finance_di
MYSQL_USER=finance
MYSQL_PASSWORD=your_db_password

# ─── Docker Keycloak ───
KEYCLOAK_ADMIN=admin
KEYCLOAK_ADMIN_PASSWORD=your_keycloak_password
KC_DB_USERNAME=keycloak
KC_DB_PASSWORD=your_keycloak_db_password

# ─── Laravel DB (samakan dengan MySQL di atas) ───
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=finance_di
DB_USERNAME=finance
DB_PASSWORD=your_db_password
```

### 3. Jalankan

```bash
docker compose up -d --build
```

### 4. Generate APP_KEY

```bash
docker compose exec php php artisan key:generate
```

### 5. Jalankan migrasi

```bash
docker compose exec php php artisan migrate
```

### 6. Akses aplikasi

- **Aplikasi**: http://localhost:8080
- **Admin Panel**: http://localhost:8080/admin
- **phpMyAdmin**: http://localhost:8081
- **Keycloak**: http://localhost:8180/admin

## Perintah Docker Umum

```bash
# Jalankan semua service
docker compose up -d

# Lihat log
docker compose logs -f

# Lihat log service tertentu
docker compose logs -f php

# Masuk ke container PHP
docker compose exec php bash

# Jalankan artisan
docker compose exec php php artisan <command>

# Jalankan Composer
docker compose exec php composer <command>

# Stop semua service
docker compose down

# Stop dan hapus volume
docker compose down -v
```

## Environment Variables

### Application

| Variable | Default | Keterangan |
|----------|---------|------------|
| `APP_NAME` | `Laravel` | Nama aplikasi |
| `APP_ENV` | `local` | Environment (local/production) |
| `APP_KEY` | - | Encryption key (generate dengan `php artisan key:generate`) |
| `APP_DEBUG` | `true` | Debug mode |
| `APP_URL` | `http://localhost:8080` | URL aplikasi |

### Database

| Variable | Default | Keterangan |
|----------|---------|------------|
| `DB_CONNECTION` | `mysql` | Driver database |
| `DB_HOST` | `mysql` | Host MySQL (nama service Docker) |
| `DB_PORT` | `3306` | Port MySQL |
| `DB_DATABASE` | `finance_di` | Nama database |
| `DB_USERNAME` | `finance` | Username database |
| `DB_PASSWORD` | - | Password database |

### Docker MySQL

| Variable | Default | Keterangan |
|----------|---------|------------|
| `MYSQL_ROOT_PASSWORD` | - | Password root MySQL |
| `MYSQL_DATABASE` | `finance_di` | Database yang dibuat otomatis |
| `MYSQL_USER` | `finance` | User yang dibuat otomatis |
| `MYSQL_PASSWORD` | - | Password user |

### Docker Keycloak

| Variable | Default | Keterangan |
|----------|---------|------------|
| `KEYCLOAK_ADMIN` | `admin` | Username admin Keycloak |
| `KEYCLOAK_ADMIN_PASSWORD` | - | Password admin Keycloak |
| `KC_DB_USERNAME` | `keycloak` | Username DB Keycloak |
| `KC_DB_PASSWORD` | - | Password DB Keycloak |

## Development

### Code Style

```bash
# Cek format
vendor/bin/pint --test

# Auto-fix format
vendor/bin/pint
```

### Static Analysis

```bash
vendor/bin/phpstan analyse --memory-limit=2G
```

### Testing

```bash
vendor/bin/pest

# Jalankan test tertentu
vendor/bin/pest --filter=ExampleTest

# Parallel
vendor/bin/pest --parallel
```

### Commit Convention

Projek ini menggunakan [Conventional Commits](https://www.conventionalcommits.org/).

**Format:**
```
<type>(<scope>): <subject>
```

**Types:** `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `test`, `build`, `ci`, `chore`, `revert`

**Scopes:** `auth`, `payment`, `report`, `settings`, `ui`, `api`, `db`, `deps`, `docker`

**Contoh:**
```
feat(auth): tambah login dengan Keycloak
fix(payment): kalkulasi pajak tidak akurat
docs(docker): tambah dokumentasi setup
```

### Cara Commit

#### Menggunakan Commitizen (Recommended)

```bash
npm run commit
```

Perintah ini otomatis menjalankan `git add .` lalu membuka interactive prompt commitizen:

1. **Pilih type** → `feat`, `fix`, `docs`, dll.
2. **Pilih scope** → `auth`, `payment`, `ui`, dll. (atau custom scope)
3. **Isi subject** → singkatan perubahan (max 100 karakter)
4. **Isi body** (opsional) → penjelasan lebih detail
5. **Isi breaking changes** (opsional) → jika ada perubahan tidak kompatibel
6. **Isi issues** (opsional) → reference ke issue, e.g. `Closes #123`

Contoh interaksi:
```
? Select the type of change:   feat: A new feature
? Select the scope:            auth
? Short description:           tambah login dengan Keycloak
? Longer description:          (optional, press Enter to skip)
? Breaking changes?            No
? Issues closed:               (optional, press Enter to skip)

feat(auth): tambah login dengan Keycloak
```

#### Manual Commit

```bash
git add .
git commit -m "feat(auth): tambah login dengan Keycloak"
```

> Lefthook akan menjalankan git hooks otomatis (pint, phpstan, commitlint). Jika ada error, perbaiki dulu sebelum commit berhasil.

### Git Hooks

Projek menggunakan [Lefthook](https://github.com/evilmartians/lefthook) untuk git hooks:

| Hook | Yang dijalankan | Keterangan |
|------|-----------------|------------|
| **pre-commit** | `pint {staged_files}` + `phpstan analyse` | Format & static analysis otomatis |
| **commit-msg** | `commitlint --edit` | Validasi format Conventional Commits |
| **pre-push** | `pint --test` + `pest` | Semua test harus pass sebelum push |

> Pint akan auto-fix format pada staged files (`stage_fixed: true`). Jika phpstan menemukan error, commit akan gagal.

## CI/CD

Pipeline di `.github/workflows/ci.yml` menjalankan 4 job:

1. **Lint & Format** - `pint --test`
2. **Static Analysis** - `phpstan analyse`
3. **Tests** - `pest --parallel` (dengan MySQL service)
4. **Build Assets** - `npm ci && npm run build`

Dijalankan otomatis pada push ke `main`/`dev` dan pull request ke `main`.

## Produksi

```bash
# Jalankan dengan production compose
docker compose -f docker-compose.prod.yml up -d --build

# Pastikan .env sudah dikonfigurasi untuk production
# APP_ENV=production
# APP_DEBUG=false
# MYSQL_ROOT_PASSWORD=<strong_password>
# KEYCLOAK_ADMIN_PASSWORD=<strong_password>
```

Produksi menggunakan:
- Nginx dengan security headers (HSTS, CSP, XSS Protection)
- PHP tanpa Xdebug, OPcache aktif
- MySQL dan Redis tidak di-expose ke luar
- User non-root untuk container PHP
- Health check untuk semua service

## License

Proprietary - PT Dirgantara. Hak Cipta Dilindungi.

Penggunaan terbatas untuk penggunaan internal saja. Dilarang mendistribusikan, menjual, atau melakukan reverse engineering. Lihat [LICENSE](LICENSE) untuk detail lengkap.
