# Purchase Receipts System - Installation Guide

## 📋 Overview
Sistem kwitansi pembelian inventory yang terintegrasi dengan stock_outgoings dan accounting system.

## 🗄️ Database Tables yang Dibuat

### 1. **purchase_receipts** (Master Kwitansi)
- Menyimpan data kwitansi pembelian
- Upload file kwitansi (PDF/Image) dengan path
- Status workflow: draft → submitted → approved/rejected
- Link ke stock_outgoings

### 2. **purchase_receipt_items** (Detail Items Kwitansi)
- One receipt, many items
- Auto-calculate subtotal (qty × price)
- Link ke inventory_id
- Support multiple items dalam satu kwitansi

### 3. **accounting_transactions** (Transaksi Akuntansi)
- Auto-generated dari stock_outgoings
- Link ke COA (Chart of Accounts)
- Link ke transaction_types (Debit/Credit)
- Support double-entry accounting

### 4. **transaction_types** (Tipe Transaksi)
- Master data untuk jenis transaksi
- Sample: DBT (Debit), CRT (Credit), PURCHASE, PAYMENT

### 5. **chart_of_accounts** (Chart of Accounts)
- Daftar akun akuntansi
- Hierarchy support (parent-child)
- Sample: Aset, Persediaan, Hutang Usaha, Beban

### 6. **stock_outgoings** (Updated)
- Added: `purchase_receipt_id` (FK)
- Added: `receipt_verified` (boolean)

## 🚀 Installation Steps

### Method 1: Via phpMyAdmin (Recommended)

1. **Buka phpMyAdmin**
   ```
   http://localhost/phpmyadmin
   ```

2. **Select Database**
   - Pilih database `asahi_inventories` di sidebar kiri

3. **Import SQL File**
   - Klik tab "SQL" di atas
   - Copy-paste isi file `purchase_receipts_system.sql`
   - Klik tombol "Go" / "Kirim"

4. **Verify Installation**
   - Klik tab "Structure" / "Struktur"
   - Cek apakah tables berikut sudah ada:
     - ✅ purchase_receipts
     - ✅ purchase_receipt_items
     - ✅ accounting_transactions
     - ✅ transaction_types
     - ✅ chart_of_accounts
   - Buka table `stock_outgoings`, cek column baru:
     - ✅ purchase_receipt_id
     - ✅ receipt_verified

### Method 2: Via MySQL Command Line

```bash
# Navigate to migration folder
cd d:\xampp\htdocs\asahi\database\migrations

# Execute SQL (jika root tanpa password)
d:\xampp\mysql\bin\mysql.exe -u root asahi_inventories < purchase_receipts_system.sql

# Execute SQL (jika root dengan password)
d:\xampp\mysql\bin\mysql.exe -u root -p asahi_inventories < purchase_receipts_system.sql
```

### Method 3: Via PHP Migration Script

1. **Edit run_migration.php** - Update password jika perlu:
   ```php
   $config = [
       'host' => 'localhost',
       'username' => 'root',
       'password' => 'YOUR_PASSWORD_HERE', // Ganti dengan password MySQL Anda
       'database' => 'asahi_inventories',
       'charset' => 'utf8mb4'
   ];
   ```

2. **Run migration**:
   ```bash
   cd d:\xampp\htdocs\asahi\database\migrations
   php run_migration.php
   ```

## 📊 Database Schema Diagram

```
┌─────────────────────────┐
│  purchase_receipts      │
│─────────────────────────│
│  id (PK)                │
│  receipt_number         │◄─────┐
│  receipt_date           │      │
│  supplier_name          │      │
│  file_path ★            │      │
│  file_name              │      │
│  total_amount           │      │
│  status                 │      │
└─────────────────────────┘      │
                                 │
                                 │ FK
┌─────────────────────────┐      │
│ purchase_receipt_items  │      │
│─────────────────────────│      │
│  id (PK)                │      │
│  purchase_receipt_id────┼──────┘
│  inventory_id (FK)      │
│  quantity               │
│  unit_price             │
│  subtotal (calculated)  │
└─────────────────────────┘


┌─────────────────────────┐      ┌──────────────────────┐
│  stock_outgoings        │      │ accounting_          │
│─────────────────────────│      │ transactions         │
│  id (PK)                │      │──────────────────────│
│  inventory_id           │      │  id (PK)             │
│  purchase_receipt_id ★──┼─────►│  stock_outgoing_id   │
│  receipt_verified ★     │      │  coa_id (FK)         │
│  ...                    │      │  transaction_type_id │
└─────────────────────────┘      │  debit_amount        │
                                 │  credit_amount       │
                                 └──────────────────────┘
                                         │
                    ┌────────────────────┴─────────────────────┐
                    │                                          │
                    ▼                                          ▼
         ┌──────────────────────┐              ┌──────────────────────┐
         │ chart_of_accounts    │              │ transaction_types    │
         │──────────────────────│              │──────────────────────│
         │  id (PK)             │              │  id (PK)             │
         │  account_code        │              │  code                │
         │  account_name        │              │  name                │
         │  account_type        │              │  is_debit            │
         │  normal_balance      │              └──────────────────────┘
         └──────────────────────┘
```

## ⚙️ Features Implemented

### 1. Auto-Calculate
- ✅ Subtotal di `purchase_receipt_items` (via trigger)
- ✅ Total amount di `purchase_receipts` (via trigger)

### 2. File Upload Support
- ✅ `file_path`: Path penyimpanan di server
- ✅ `file_name`: Nama file asli
- ✅ `file_size`: Ukuran file (bytes)
- ✅ `file_type`: MIME type (image/jpeg, application/pdf, dll)

### 3. Workflow Status
```
draft → submitted → approved/rejected
                 ↓
              cancelled
```

### 4. Accounting Integration
- Link `stock_outgoings` → `accounting_transactions`
- Auto-generate journal entries
- Double-entry accounting ready

## 🔗 Associations (CakePHP)

```php
// PurchaseReceipt hasMany PurchaseReceiptItem
// PurchaseReceipt hasMany StockOutgoing
// PurchaseReceipt hasMany AccountingTransaction

// PurchaseReceiptItem belongsTo PurchaseReceipt
// PurchaseReceiptItem belongsTo Inventory

// StockOutgoing belongsTo PurchaseReceipt

// AccountingTransaction belongsTo StockOutgoing
// AccountingTransaction belongsTo PurchaseReceipt
// AccountingTransaction belongsTo ChartOfAccount
// AccountingTransaction belongsTo TransactionType
```

## 📝 Next Steps

1. ✅ **Run SQL Migration** (pilih method di atas)
2. ⏳ **Bake Models**
3. ⏳ **Create Upload Controller**
4. ⏳ **Build Dynamic Form UI**
5. ⏳ **Implement Accounting Trigger**

## ⚠️ Important Notes

- **File Upload Path**: Default akan ke `webroot/uploads/receipts/`
- **Supported Formats**: JPG, PNG, PDF
- **Max File Size**: Configure di PHP.ini (default 2MB)
- **Foreign Keys**: Gunakan ON DELETE RESTRICT untuk purchase_receipts agar tidak terhapus jika ada stock_outgoings
- **Triggers**: Auto-calculate sudah aktif setelah migration

## 🧪 Testing

Setelah migration berhasil, test dengan query:

```sql
-- Check tables created
SHOW TABLES LIKE '%purchase%';
SHOW TABLES LIKE '%accounting%';

-- Check sample data
SELECT * FROM transaction_types;
SELECT * FROM chart_of_accounts;

-- Check stock_outgoings alteration
DESCRIBE stock_outgoings;
```

---

**Created:** 2025-11-04  
**Database:** asahi_inventories  
**Connection:** default
