
# MASTER BAKE & DATABASE MAPPING GUIDE

## Purpose
Panduan utama untuk semua proses bake, mapping database, dan instruksi memory AI. Selalu update file ini jika ada perubahan struktur database atau nama koneksi.

---



## 1. Database Connection Mapping (Selalu Cek Sebelum Bake)

**Prinsip Utama:**
- Setiap tabel hanya ada di satu database (tidak boleh duplikat).
- Nama koneksi dan database harus sesuai dengan `config/app_datasources.php`.

### Database Connections (config/app_datasources.php)

| Connection Name                        | Database Name                          | Contoh Tabel Utama                |
|----------------------------------------|----------------------------------------|-----------------------------------|
| default                                | cms_masters                            | Users, Roles, Menus               |
| cms_masters                            | cms_masters                            | MasterData, Settings              |
| cms_lpk_candidates                     | cms_lpk_candidates                     | Candidates, CandidateStatuses     |
| cms_lpk_candidate_documents            | cms_lpk_candidate_documents            | CandidateDocuments                |
| cms_tmm_apprentices                    | cms_tmm_apprentices                    | Apprentices, ApprenticeStatuses   |
| cms_tmm_apprentice_documents           | cms_tmm_apprentice_documents           | ApprenticeDocuments               |
| cms_tmm_apprentice_document_ticketings | cms_tmm_apprentice_document_ticketings | DocumentTicketings                |
| cms_tmm_organizations                  | cms_tmm_organizations                  | Organizations, OrgTypes           |
| cms_tmm_stakeholders                   | cms_tmm_stakeholders                   | Stakeholders, StakeholderTypes    |
| cms_tmm_trainees                       | cms_tmm_trainees                       | Trainees, TraineeStatuses         |
| cms_tmm_trainee_accountings            | cms_tmm_trainee_accountings            | TraineeAccountings                |
| cms_tmm_trainee_trainings              | cms_tmm_trainee_trainings              | TraineeTrainings                  |
| cms_tmm_trainee_training_scorings      | cms_tmm_trainee_training_scorings      | TrainingScorings                  |

**Contoh Penggunaan di Table Class:**
```php
// src/Model/Table/TraineesTable.php
$this->setConnection(ConnectionManager::get('cms_tmm_trainees'));
```

**Bake Command:**
```powershell
bin\cake bake all Trainees --connection cms_tmm_trainees --force
```

---


## 2. Cross-Database Associations

**Jika relasi ke tabel di database/koneksi lain:**
- Tambahkan `'strategy' => 'select'` pada definisi association.

**Contoh:**
```php
$this->belongsTo('Organizations', [
    'foreignKey' => 'organization_id',
    'strategy' => 'select', // WAJIB untuk cross-database
]);
```

**Jika menggunakan alias:**
```php
$this->belongsTo('ApprovedByTrainees', [
    'className' => 'Trainees',
    'foreignKey' => 'approved_by_trainee_id',
    'strategy' => 'select',
]);
```

---


## 3. Post-Bake Checklist

- Controller file tidak kosong (>5 KB)
- Semua cross-database association sudah pakai `'strategy' => 'select'`
- Semua relasi alias sudah pakai `'className'`
- Clear cache: `bin\cake cache clear_all`
- Test di browser (Ctrl+Shift+R)

---


## 4. Troubleshooting & Error Handling

**Jika error "Base table or view not found":**
- Cek koneksi di Table model (`setConnection` dan `defaultConnectionName()`)
- Pastikan `'strategy' => 'select'` sudah ditambah untuk cross-database
- Validasi foreign key dan nama tabel

---


## 5. Struktur Project & Fitur Bake Otomatis

Setiap bake menghasilkan otomatis:
- Controller: add/edit/delete/export
- Table model: relasi, cross-db, alias
- Entity
- Views: index, view, add, edit (tema mobile/teal)
- Export: CSV, Excel, PDF, Print
- Upload gambar/file otomatis (image/file fields)
- Filter & search di index (client-side & AJAX)
- Responsive mobile (lihat TEMPLATE_IMPROVEMENTS.css)

---


## 6. Cara Update File Ini

Jika ada perubahan database/koneksi:
- Update mapping di atas sesuai config/app_datasources.php
- Tambahkan/ubah nama koneksi dan tabel utama
- Hapus file instruksi lain yang sudah digabung ke sini

---


## 7. Contoh Multi-Database Setup

```php
'Datasources' => [
    'default' => [ ... ],
    'cms_tmm_trainees' => [ ... ],
    'cms_tmm_organizations' => [ ... ],
    // dst
],
```

---


## 8. Quick Bake Reference

```powershell
# Default (cms_masters)
bin\cake bake all Users --connection default --force
# Multi-database (cms_tmm_trainees)
bin\cake bake all Trainees --connection cms_tmm_trainees --force
```

---


## 9. Dokumentasi & AI Agent Memory

File ini adalah referensi utama untuk instruksi chat, memory AI, dan semua proses bake. Selalu update jika ada perubahan database/koneksi/tabel.
