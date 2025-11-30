# Purchase Receipts System - Updated Workflow

## ✅ Perubahan Struktur

### BEFORE (Salah):
```
purchase_receipts → stock_outgoings
```

### AFTER (Benar):
```
purchase_receipts → stock_incomings (Pembelian/Incoming)
```

## 📋 Database Associations

```
PurchaseReceipts
├── hasMany PurchaseReceiptItems
├── hasMany StockIncomings  ← UPDATED
└── hasMany AccountingTransactions

StockIncomings
├── belongsTo PurchaseReceipts  ← NEW
└── belongsTo Inventory

PurchaseReceiptItems
├── belongsTo PurchaseReceipts
└── belongsTo Inventory

AccountingTransactions
├── belongsTo PurchaseReceipts
├── belongsTo StockIncomings  ← UPDATED
├── belongsTo ChartOfAccounts
└── belongsTo TransactionTypes
```

## 🔄 Workflow

### 1. Upload Kwitansi
```
User → Upload PDF/Image kwitansi
     → Save to: webroot/uploads/receipts/
     → Create record: purchase_receipts
     → Status: draft
```

### 2. Add Items (Dynamic Form)
```
User → Click [Add Field] button
     → Input: item_description, quantity, unit_price
     → Save to session (temporary)
     → Can add multiple items
     → Submit all items → create purchase_receipt_items
```

### 3. Register Stock Incoming
```
User → Select purchase_receipt_id
     → Register inventory items to stock_incomings
     → Link: stock_incomings.purchase_receipt_id
     → Status: receipt_verified = 1
```

### 4. Auto-Generate Accounting
```
Trigger: afterSave() in StockIncomingsTable
       → Create accounting_transactions
       → Debit: Persediaan Inventory (1-2100)
       → Credit: Hutang Usaha (2-1000)
       → Amount: from purchase_receipt_items.subtotal
```

## 🗄️ Execute SQL Commands

### Via phpMyAdmin:

**Step 1: Create All Tables**
```sql
-- Copy paste from: purchase_receipts_system.sql
-- Creates: purchase_receipts, purchase_receipt_items, 
--          accounting_transactions, transaction_types, chart_of_accounts
```

**Step 2: Fix Association (if needed)**
```sql
-- Copy paste from: fix_stock_incomings.sql
-- Removes: purchase_receipt_id from stock_outgoings
-- Adds: purchase_receipt_id to stock_incomings
```

## 🎯 Baking Commands

After SQL execution:

```bash
cd d:\xampp\htdocs\asahi_v3

# Bake Models
php bin\cake.php bake model PurchaseReceipts -c default -f
php bin\cake.php bake model PurchaseReceiptItems -c default -f
php bin\cake.php bake model AccountingTransactions -c default -f
php bin\cake.php bake model TransactionTypes -c default -f
php bin\cake.php bake model ChartOfAccounts -c default -f

# Re-bake StockIncomings to get new association
php bin\cake.php bake model StockIncomings -c default -f

# Bake Controllers & Views
php bin\cake.php bake controller PurchaseReceipts -c default -f
php bin\cake.php bake controller PurchaseReceiptItems -c default -f

php bin\cake.php bake template PurchaseReceipts -c default -f
php bin\cake.php bake template PurchaseReceiptItems -c default -f
```

## 📁 File Upload Configuration

Create directory:
```
webroot/uploads/receipts/
```

Upload settings in PurchaseReceiptsController:
```php
$allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
$maxFileSize = 5 * 1024 * 1024; // 5MB
$uploadPath = WWW_ROOT . 'uploads' . DS . 'receipts' . DS;
```

## 🧪 Testing Workflow

1. **Create purchase receipt**
   - Upload kwitansi.pdf
   - Enter supplier info
   - Save (status: draft)

2. **Add items**
   - Click [Add Field]
   - Item 1: Laptop, qty=2, price=5000000
   - Item 2: Mouse, qty=10, price=50000
   - Submit → auto-calculate total

3. **Register to stock_incomings**
   - Select inventory items
   - Link to purchase_receipt_id
   - Save → trigger accounting

4. **Verify accounting**
   - Check accounting_transactions
   - Debit: Persediaan Inventory
   - Credit: Hutang Usaha
   - Amount: match total

---

**Status:** Ready to bake after SQL execution  
**Next:** Execute SQL → Bake models → Build UI
