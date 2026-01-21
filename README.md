# Cafe Landing Page - Project Documentation

Project landing page untuk cafe dengan fitur order menu, cart, payment QRIS, dan admin panel.

---

## Tech Stack

| Layer | Teknologi | Keterangan |
|-------|-----------|------------|
| **Backend** | Laravel 11 | REST API |
| **Frontend** | React 18 + Vite | SPA terpisah dari backend |
| **Styling** | Tailwind CSS | Utility-first CSS framework |
| **Database** | MySQL | Via Laragon |
| **Admin Panel** | Filament PHP | CRUD menu, orders, promo |
| **Payment** | Midtrans | QRIS Dinamis |
| **Auth** | Laravel Sanctum | Token-based authentication |
| **HTTP Client** | Axios | API calls dari React |
| **State Management** | React Context | Cart state |

---

## Arsitektur Sistem

```
┌─────────────────────┐         ┌─────────────────────┐
│   REACT (Frontend)  │  HTTP   │  LARAVEL (Backend)  │
│                     │ ◄─────► │                     │
│  localhost:5173     │  API    │  localhost:8000     │
└─────────────────────┘         └─────────────────────┘
        │                               │
        │                               ▼
        │                       ┌───────────────┐
        │                       │    MySQL      │
        │                       │   (Laragon)   │
        │                       └───────────────┘
        │                               │
        │                               ▼
        │                       ┌───────────────┐
        └──────────────────────►│   Midtrans    │
                                │  (Payment)    │
                                └───────────────┘
```

---

## Struktur Folder (Monorepo)

```
D:\Landingpage_Cafe\
├── backend\                    # Laravel API
│   ├── app\
│   │   ├── Http\
│   │   │   └── Controllers\
│   │   │       └── Api\        # API Controllers
│   │   ├── Models\             # Eloquent Models
│   │   ├── Services\           # Business Logic
│   │   └── Filament\           # Admin Panel
│   ├── database\
│   │   ├── migrations\         # Database Migrations
│   │   └── seeders\            # Dummy Data
│   └── routes\
│       └── api.php             # API Routes
│
├── frontend\                   # React App
│   ├── src\
│   │   ├── components\         # Reusable Components
│   │   ├── pages\              # Page Components
│   │   ├── context\            # React Context (Cart)
│   │   └── services\           # API Service (Axios)
│   └── public\
│
├── flowchart\                  # PlantUML Diagrams
│   ├── Customer_Flow.puml
│   ├── Admin_Flow.puml
│   ├── Database_ERD.puml
│   └── System_Architecture.puml
│
└── README.md
```

---

## Database Schema

### Tables

#### users (Admin)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary Key |
| name | varchar | Nama admin |
| email | varchar | Email login |
| password | varchar | Password hash |
| role | enum | 'admin', 'staff' |

#### categories
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary Key |
| name | varchar | Nama kategori (Makanan, Minuman, dll) |
| slug | varchar | URL-friendly name |
| is_active | boolean | Status aktif |

#### menu_items
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary Key |
| category_id | bigint | Foreign Key ke categories |
| name | varchar | Nama menu |
| description | text | Deskripsi menu |
| price | decimal | Harga |
| image | varchar | Path gambar |
| is_available | boolean | Ketersediaan |

#### orders
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary Key |
| order_number | varchar | Nomor order unik |
| customer_name | varchar | Nama pemesan |
| table_number | varchar | No meja / takeaway |
| notes | text | Catatan pesanan |
| subtotal | decimal | Total sebelum fee |
| total | decimal | Total akhir |
| status | enum | 'pending', 'confirmed', 'completed', 'cancelled' |
| payment_method | varchar | 'qris', 'cash' |
| payment_status | enum | 'pending', 'paid', 'failed' |
| paid_at | timestamp | Waktu pembayaran |

#### order_items
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary Key |
| order_id | bigint | Foreign Key ke orders |
| menu_item_id | bigint | Foreign Key ke menu_items |
| quantity | int | Jumlah |
| price | decimal | Harga saat order |
| subtotal | decimal | quantity * price |

#### promotions
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary Key |
| title | varchar | Judul promo |
| description | text | Deskripsi |
| image | varchar | Banner image |
| start_date | date | Tanggal mulai |
| end_date | date | Tanggal berakhir |
| is_active | boolean | Status aktif |
| show_as_popup | boolean | Tampil sebagai popup |

#### cafe_settings
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary Key |
| key | varchar | Nama setting |
| value | text | Nilai setting |

---

## API Endpoints

### Public Endpoints (No Auth)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/cafe-profile` | Info cafe (nama, alamat, jam buka) |
| GET | `/api/categories` | Daftar semua kategori |
| GET | `/api/menus` | Daftar semua menu |
| GET | `/api/menus/{id}` | Detail satu menu |
| GET | `/api/promotions/active` | Promo aktif untuk popup |
| POST | `/api/orders` | Buat order baru |
| POST | `/api/payment/create` | Generate QRIS payment |
| POST | `/api/payment/callback` | Midtrans webhook callback |

### Admin Endpoints (Auth Required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/admin/login` | Login admin |
| GET | `/api/admin/orders` | List semua orders |
| PUT | `/api/admin/orders/{id}` | Update status order |

---

## Fitur Aplikasi

### Customer (Frontend)

1. **Landing Page**
   - Hero section dengan foto cafe
   - Profile cafe (nama, alamat, jam operasional)
   - Gallery foto
   - Info kontak

2. **Menu Page**
   - Kategori menu (tabs/filter)
   - Grid menu items
   - Tombol "Add to Cart"
   - Harga dan foto menu

3. **Cart**
   - List item yang dipilih
   - Update quantity (+/-)
   - Remove item
   - Total harga
   - Tombol checkout

4. **Checkout**
   - Form: nama, no meja, catatan
   - Pilih metode bayar
   - Generate & tampilkan QRIS
   - Status pembayaran

5. **Promo Popup**
   - Modal popup saat pertama buka web
   - Tampilkan promo aktif
   - Bisa ditutup

### Admin (Filament)

1. **Dashboard**
   - Statistik penjualan hari ini
   - Total orders
   - Revenue

2. **Kelola Menu**
   - CRUD menu items
   - Upload foto
   - Set harga
   - Set ketersediaan

3. **Kelola Kategori**
   - CRUD kategori
   - Urutan tampil

4. **Kelola Orders**
   - List orders masuk
   - Update status (confirm, complete, cancel)
   - Filter by status/tanggal

5. **Kelola Promo**
   - CRUD promo/banner
   - Set periode aktif
   - Toggle popup

6. **Settings**
   - Edit info cafe
   - Update kontak

---

## Setup & Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL (Laragon)

### Backend Setup

```bash
# 1. Masuk ke folder project
cd D:\Landingpage_Cafe

# 2. Buat Laravel project
composer create-project laravel/laravel backend

# 3. Masuk ke backend
cd backend

# 4. Copy .env
cp .env.example .env

# 5. Generate key
php artisan key:generate

# 6. Setup database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=cafe_db
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Install Sanctum
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# 8. Install Filament
composer require filament/filament
php artisan filament:install --panels

# 9. Install Midtrans
composer require midtrans/midtrans-php

# 10. Run migrations
php artisan migrate

# 11. Create admin user
php artisan make:filament-user

# 12. Link storage (Wajib untuk gambar menu/promo)
php artisan storage:link

# 13. Run server
php artisan serve
```

### Frontend Setup

```bash
# 1. Dari root folder
cd D:\Landingpage_Cafe

# 2. Buat React project
npm create vite@latest frontend -- --template react

# 3. Masuk ke frontend
cd frontend

# 4. Install dependencies
npm install axios react-router-dom

# 5. Install Tailwind
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p

# 6. Run dev server
npm run dev
```

---

## Environment Variables

### Backend (.env)

```env
APP_NAME="Cafe App"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cafe_db
DB_USERNAME=root
DB_PASSWORD=

# Midtrans
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false

# CORS
FRONTEND_URL=http://localhost:5173
```

### Frontend (.env)

```env
VITE_API_URL=http://localhost:8000/api
```

---

## Midtrans Setup

1. Daftar di https://dashboard.midtrans.com
2. Pilih environment **Sandbox** untuk testing
3. Dapatkan **Server Key** dan **Client Key**
4. Setup Notification URL: `http://your-domain.com/api/payment/callback`

### Testing QRIS
- Gunakan Midtrans Simulator di dashboard
- Scan QR dengan simulator untuk test payment

---

## Development Commands

### Backend
```bash
# Run server
php artisan serve

# Run migrations
php artisan migrate

# Fresh migrate + seed
php artisan migrate:fresh --seed

# Create model with migration
php artisan make:model ModelName -m

# Create controller
php artisan make:controller Api/ControllerName

# Create Filament resource
php artisan make:filament-resource ResourceName
```

### Frontend
```bash
# Run dev server
npm run dev

# Build production
npm run build

# Preview build
npm run preview
```

---

## Flowchart Files

Diagram PlantUML tersedia di folder `flowchart/`:

| File | Description |
|------|-------------|
| `Customer_Flow.puml` | Flow customer dari buka web sampai bayar |
| `Admin_Flow.puml` | Flow admin manage menu & orders |
| `Database_ERD.puml` | Entity Relationship Diagram |
| `System_Architecture.puml` | Arsitektur sistem keseluruhan |

Buka di https://www.plantuml.com/plantuml atau VS Code extension PlantUML.

---

## Urutan Implementasi

1. **Phase 1: Backend Setup**
   - Laravel project initialization
   - Database migrations
   - Models & relationships
   - API endpoints (public)

2. **Phase 2: Admin Panel**
   - Setup Filament
   - CRUD resources
   - Dashboard widgets

3. **Phase 3: Seed Data**
   - Dummy categories
   - Dummy menu items
   - Test admin user

4. **Phase 4: Frontend**
   - React + Vite setup
   - Tailwind configuration
   - Pages & components
   - Cart context
   - API integration

5. **Phase 5: Payment**
   - Midtrans integration
   - QRIS generation
   - Webhook handler
   - Payment status update

---

## Resources & Links

- [Laravel Documentation](https://laravel.com/docs)
- [React Documentation](https://react.dev)
- [Filament Documentation](https://filamentphp.com/docs)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Midtrans Documentation](https://docs.midtrans.com)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Axios](https://axios-http.com/docs/intro)
