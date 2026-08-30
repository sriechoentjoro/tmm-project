# 📊 Role Dashboard System — Complete Documentation

**TMM Apprentice Management System**
**Component:** Role-based Dashboard Routing & Views
**Controller:** `src/Controller/DashboardController.php`
**Templates:** `src/Template/Dashboard/*.ctp`
**Last updated:** 2026-07-02

---

## 📋 Table of Contents

1. [Overview](#-overview)
2. [Quick Reference](#-quick-reference)
3. [Architecture & Request Flow](#-architecture--request-flow)
4. [Role Detection & Priority Order](#-role-detection--priority-order)
5. [Dashboard Details (Per Role)](#-dashboard-details-per-role)
6. [Helper Methods API Reference](#-helper-methods-api-reference)
7. [Database Connections](#-database-connections)
8. [Visual Design System](#-visual-design-system)
9. [Fallbacks & Error Handling](#-fallbacks--error-handling)
10. [How to Add a New Role Dashboard](#-how-to-add-a-new-role-dashboard)
11. [Known Limitations & Roadmap](#-known-limitations--roadmap)
12. [Related Files & Documentation](#-related-files--documentation)

---

## 🎯 Overview

The Role Dashboard System is the **landing experience** of the TMM application. When any
authenticated user visits the site root (`/`), `DashboardController::index()` inspects the
user's roles and renders a **dashboard tailored to that role** — each with its own template,
statistics, recent-activity feed, and quick actions.

Key characteristics:

- ✅ **Single entry point** — one route (`/`), one `index()` action, six specialized dashboards
- ✅ **Role-driven routing** — decided by `role_names` stored in the Auth session at login
- ✅ **Cross-database statistics** — counts and recent records pulled from multiple CMS databases
  via `ConnectionManager`
- ✅ **Fail-soft data loading** — every statistic helper catches exceptions and degrades to
  `0` / `[]` so the dashboard never white-screens if a table or connection is unavailable
- ✅ **Institution scoping** — the LPK dashboard filters all data to the logged-in user's
  own institution

---

## ⚡ Quick Reference

| # | Role(s) | Dashboard Method | Template (`src/Template/Dashboard/`) | Focus |
|---|---------|------------------|--------------------------------------|-------|
| 1 | `administrator` | `administratorDashboard()` | `admin_dashboard.ctp` | Full system overview + admin quick actions |
| 2 | `management`, `director` | `managementDashboard()` | `management_dashboard.ctp` | Read-only analytics overview |
| 3 | `tmm-recruitment` | `recruitmentDashboard()` | `recruitment_dashboard.ctp` | Candidate pipeline management |
| 4 | `tmm-training` | `trainingDashboard()` | `training_dashboard.ctp` | Trainee progress tracking |
| 5 | `tmm-documentation` | `documentationDashboard()` | `documentation_dashboard.ctp` | Apprentice document processing |
| 6 | `lpk-penyangga` | `lpkDashboard()` | `lpk_dashboard.ctp` | Institution-scoped candidate management |
| — | *(no matching role)* | fallback in `index()` | `index.ctp` | Generic zeroed dashboard |

**Public URLs**

| URL | Action | Notes |
|-----|--------|-------|
| `/` | `Dashboard::index` | Root route (`config/routes.php:75`) — role-routed |
| `/dashboard/lpk` | `Dashboard::lpk` | Direct public entry to the LPK dashboard |
| `/dashboard/process-flow` | `Dashboard::processFlow` | Multi-language process flow documentation (`ind`/`eng`/`jpn`) |

---

## 🏗 Architecture & Request Flow

```
                        ┌──────────────────────────────┐
                        │   GET /  (authenticated)     │
                        └──────────────┬───────────────┘
                                       │
                        ┌──────────────▼───────────────┐
                        │  DashboardController::index() │
                        │  $roles = user['role_names']  │
                        └──────────────┬───────────────┘
                                       │  (checked in this order)
     ┌──────────────┬──────────────┬───┴──────────┬──────────────┬──────────────┐
     │              │              │              │              │              │
administrator  management/    tmm-          tmm-          tmm-          lpk-
     │          director      recruitment   training      documentation penyangga
     ▼              ▼              ▼              ▼              ▼              ▼
admin_        management_    recruitment_  training_     documentation_ lpk_
dashboard     dashboard      dashboard     dashboard     dashboard      dashboard
     │              │              │              │              │              │
     └──────────────┴──────────────┴──────┬───────┴──────────────┴──────────────┘
                                          │  no role matched
                                          ▼
                                    index.ctp (zeroed fallback)
```

**Data flow inside each dashboard method:**

```
dashboardMethod()
   │
   ├─► viewBuilder()->setTemplate('<role>_dashboard')
   │
   ├─► getTotalCount() / getCountByCondition()      ──►  stat cards
   │        └─ TableRegistry + ConnectionManager
   │
   ├─► getRecentRecords() / getRecentRecordsByCondition() ──► recent-activity table
   │        └─ ORDER BY created DESC, LIMIT 5–10
   │
   └─► $this->set(compact('stats', ...))            ──►  .ctp template
```

**Where `role_names` comes from:** at login, `UsersController` loads the user's roles and
stores them in the Auth session:

```php
// src/Controller/UsersController.php (login)
$user['role_names'] = collection($userEntity->roles)->extract('name')->toArray();
```

`AppController::hasRole()` uses the same array for menu building and authorization checks
throughout the app.

**Authorization:** `DashboardController::isAuthorized()` returns `true` for every
authenticated user — the *routing* itself is the access control (each role only ever
sees its own dashboard from `/`).

---

## 🔀 Role Detection & Priority Order

Roles are checked **top-down**; the **first match wins**. A user with multiple roles gets
the highest-priority dashboard:

| Priority | Role check | Dashboard |
|----------|-----------|-----------|
| 1 (highest) | `administrator` | Administrator |
| 2 | `management` **or** `director` | Management |
| 3 | `tmm-recruitment` | Recruitment |
| 4 | `tmm-training` | Training |
| 5 | `tmm-documentation` | Documentation |
| 6 | `lpk-penyangga` | LPK |
| 7 (fallback) | *(none matched)* | Generic `index.ctp` |

> 💡 **Example:** a user with roles `['tmm-training', 'administrator']` sees the
> **Administrator** dashboard, because `administrator` is checked first.

---

## 📊 Dashboard Details (Per Role)

### 1. 👑 Administrator Dashboard

- **Method:** `administratorDashboard()` · **Template:** `admin_dashboard.ctp`
- **Audience:** System administrators — full visibility across every database.

**Statistics cards (4):**

| Card | Source model | Connection |
|------|-------------|------------|
| Total Users | `Users` | `cms_authentication_authorization` |
| Total Candidates | `Candidates` | `cms_lpk_candidates` |
| Total Trainees | `Trainees` | `cms_tmm_trainees` |
| LPK Institutions | `VocationalTrainingInstitutions` | `cms_tmm_stakeholders` |

*(A fifth stat, `totalOrganizations` from `AcceptanceOrganizations`, is computed by the
controller but not currently displayed in the template.)*

**Widgets:**
- **Quick Actions** panel → Manage Users, Manage Roles, View System Logs
- **Recent Activity** panel → placeholder (*"System activity monitoring coming soon…"*);
  the controller already supplies `recentUsers` and `recentCandidates` (5 each) for future use

---

### 2. 📈 Management / Director Dashboard

- **Method:** `managementDashboard()` · **Template:** `management_dashboard.ctp`
- **Audience:** Management and directors — **read-only** overview and analytics.

**Statistics cards (3):** Total Candidates (purple), Total Trainees (teal),
Organizations (orange) — each a flex "stat-box" with icon.

**Widgets:**
- **Candidate Overview** chart panel → Chart.js placeholder, fed by `getCandidatesByMonth()`
- **Trainee Status** chart panel → Chart.js placeholder, fed by `getTraineesByStatus()`
- **Quick Links** grid (4 gradient tiles) → View Candidates · View Trainees ·
  View Organizations · View Reports

> ⚠️ Both chart data helpers currently return `[]` (see
> [Known Limitations](#-known-limitations--roadmap)).

---

### 3. 🧑‍💼 TMM Recruitment Dashboard

- **Method:** `recruitmentDashboard()` · **Template:** `recruitment_dashboard.ctp`
- **Audience:** TMM recruitment staff — candidate pipeline at a glance.

**Statistics cards (3):**

| Card | Query |
|------|-------|
| Total Candidates | all `Candidates` |
| Pending Review | `Candidates` where `status = 'pending'` |
| Approved | `Candidates` where `status = 'approved'` |

**Widgets:**
- **Recent Candidates** table (10 rows): ID, Name, LPK Institution, Created, plus
  👁 View / ✏️ Edit action buttons
- Header actions: **➕ Add Candidate**, **📋 View All**

---

### 4. 🎓 TMM Training Dashboard

- **Method:** `trainingDashboard()` · **Template:** `training_dashboard.ctp`
- **Audience:** TMM training staff — trainee progress tracking.

**Statistics cards (3), each with a deep-link:**

| Card | Query | Card link |
|------|-------|-----------|
| Total Trainees | all `Trainees` | Trainees index |
| Active Trainees | `status = 'active'` | Trainees index `?status=active` |
| Completed Trainees | `status = 'completed'` | Trainees index `?status=completed` |

**Widgets:**
- **Recent Trainees** table (10 rows): ID, Full Name, Training Institution,
  Active/Inactive badge (from `is_active`), Created, View button

---

### 5. 📄 TMM Documentation Dashboard *(enriched — pre-departure document management)*

- **Method:** `documentationDashboard()` · **Template:** `documentation_dashboard.ctp`
- **Audience:** TMM documentation staff — manages every document a trainee needs
  **before departing to Japan**. Uses the **Trainee** document system, which is what the
  role's menus grant (`TraineeSubmissionDocuments`, `TraineeRecord*`, `Tickets`).

**Statistics cards (4):**

| Card | Source | Connection |
|------|--------|------------|
| Trainees | `Trainees` | `cms_tmm_trainees` |
| Document Submissions | `TraineeSubmissionDocuments` | `cms_tmm_trainee_documents` |
| Submitted | submissions grouped by `master_document_submission_statuses.name` | `cms_tmm_trainee_documents` + `cms_masters` |
| Pending Review | same grouping, `Pending` status | " |

**Widgets:**
- **🛫 Pre-Departure Pipeline** — clickable stage strip with live counts:
  Submission Docs → Passports (`TraineeRecordPasports`) → Medical Check-Ups
  (`TraineeRecordMedicalCheckUps`) → COE / Visa (`TraineeRecordCoeVisas`) →
  Tickets & Flights (`Tickets`, ticketing DB) → 🇯🇵 Depart to Japan
- **Document Readiness per Trainee** — progress bar per trainee: distinct required
  documents submitted vs. total required document types
  (`master_trainee_submission_documents` where `is_required = 1`), with
  Complete / In Progress / Not Started badges + a Checklist shortcut
- **Submissions by Category** — progress per `master_trainee_submission_document_categories`
  entry (Sending Organization, Candidate, Legal/Regulatory, Translation);
  expected = required doc types × number of trainees
- **Recent Document Submissions** table (8 rows) with trainee name, document title,
  Submitted/Pending badge, upload date, View/Edit actions
- **Departures** table — upcoming `trainee_flights` (or most recent, when none upcoming)
  with flight number, airline, airport route and times, plus a Departure Management link
- **Quick Actions** — Add Submission, Document Checklist, Progress Tracking,
  Passport Records, COE & Visa, Medical Check-Ups, Tickets & Flights, Departure Documents

**Documentation-specific helpers** (raw cross-database SQL via `ConnectionManager`, fail-soft):
`fetchScalar()`, `fetchRows()`, `getSubmissionStatusCounts()`, `getTraineeDocumentReadiness()`,
`getDocumentCategoryProgress()`, `getRecentSubmissionDocuments()`, `getDepartureFlights()`

**Permissions:** every link targets an action granted to `tmm-documentation` via
`role_menus.granted_actions`. Full CRUD on the document controllers was granted by
`grant_documentation_role_full_access.sql` (note: `granted_actions = '*'` does **not**
mean all actions — `AppController::getMenuRolePermissions()` expands it to only the
menu's own action + `index` + `view`; full CRUD requires an explicit list).

---

### 6. 🏫 LPK Penyangga Dashboard *(institution-scoped)*

- **Method:** `lpkDashboard()` · **Template:** `lpk_dashboard.ctp`
- **Audience:** Partner LPK (vocational training institution) users.
- **Also reachable directly:** `Dashboard::lpk()` → `/dashboard/lpk`

This is the only dashboard that **filters every query by the user's institution**
(`vocational_training_institution_id` from `user['institution_id']`). The page header
shows the resolved institution name via `getInstitutionName()`.

**Statistics cards (3):** Total / Pending / Approved candidates — **for this institution only**.

**Widgets:**
- **Recent Candidates** table (10 rows, institution-filtered): ID, Full Name, Created,
  status badge, View/Edit buttons; friendly empty state (*"No candidates found for your
  institution"*)
- **Quick Actions:** ➕ Add New Candidate · 📋 View All Candidates · 📄 View Documents

**No-institution guard:** if the account has no `institution_id`, the user sees a Flash
error (*"Your account is not linked to any institution. Please contact administrator."*)
and a zeroed dashboard titled **"No Institution"** — the page still renders safely.

---

### 7. 🕳 Fallback Dashboard (no matching role)

If none of the six roles match, `index()` renders the default `index.ctp` with all
statistics set to `0` and empty recent lists (`totalCandidates`, `totalTrainees`,
`totalOrders`, `totalOrganizations`, `recentCandidates`, `recentTrainees`).

---

## 🔧 Helper Methods API Reference

All helpers live in `DashboardController` and are `protected`. Every one is wrapped in
`try/catch` and **fails soft** — returning `0`, `[]`, or `'Unknown Institution'` instead
of throwing.

| Method | Signature | Returns | Purpose |
|--------|-----------|---------|---------|
| `getTotalCount` | `($modelName, $connection = 'default')` | `int` | Total row count for a model |
| `getCountByCondition` | `($modelName, $connection, $conditions)` | `int` | Filtered count (explicit `ConnectionManager::get()`) |
| `getRecentRecords` | `($modelName, $connection, $limit = 10)` | `array` | Latest N rows, `ORDER BY created DESC` |
| `getRecentRecordsByCondition` | `($modelName, $connection, $conditions, $limit = 10)` | `array` | Latest N rows matching conditions |
| `getCountWhere` | `($modelName, $conditions)` | `int` | Filtered count using the model's own `defaultConnectionName()` (no registry option conflicts) |
| `fetchScalar` | `($connectionName, $sql)` | `mixed` | Single scalar via raw SQL on a named connection |
| `fetchRows` | `($connectionName, $sql)` | `array` | Assoc rows via raw SQL (supports cross-database joins) |
| `getSubmissionStatusCounts` | `()` | `array` | Trainee submissions grouped by status name, e.g. `['Submitted' => 12, 'Pending' => 3]` |
| `getTraineeDocumentReadiness` | `($requiredCount, $limit = 8)` | `array` | Per-trainee completion: distinct required docs submitted, percent |
| `getDocumentCategoryProgress` | `($totalTrainees)` | `array` | Submission progress per document category |
| `getRecentSubmissionDocuments` | `($limit = 8)` | `array` | Latest submissions with trainee, document title & status (joined) |
| `getDepartureFlights` | `($limit = 5)` | `[array, bool]` | trainee_flights joined with airline/airports; upcoming first, else most recent (bool = hasUpcoming) |
| `getCandidatesByMonth` | `()` | `array` | Chart data — **placeholder, returns `[]`** |
| `getTraineesByStatus` | `()` | `array` | Chart data — **placeholder, returns `[]`** |
| `getInstitutionName` | `($institutionId, $institutionType)` | `string` | Resolves LPK institution name (only `vocational_training` type supported) |

> ⚠️ **Implementation note:** `getTotalCount()` accepts a `$connection` argument but does
> **not** pass it to the table locator — it relies on each Table class declaring its own
> `defaultConnectionName()`. The other helpers resolve the connection explicitly with
> `ConnectionManager::get($connection)`. This works today because the baked Table classes
> set their connections, but keep it in mind when adding new stats (see
> [Known Limitations](#-known-limitations--roadmap)).

---

## 🗄 Database Connections

The dashboards aggregate data across the CMS multi-database setup (`config/app.php`
datasources):

| Connection name | Data used by dashboards |
|-----------------|------------------------|
| `cms_authentication_authorization` | Users (admin stats, recent users) |
| `cms_lpk_candidates` | Candidates (admin, management, recruitment, LPK) |
| `cms_tmm_trainees` | Trainees (admin, management, training) |
| `cms_tmm_stakeholders` | AcceptanceOrganizations, VocationalTrainingInstitutions |
| `cms_tmm_trainee_documents` | TraineeSubmissionDocuments, TraineeRecordPasports / CoeVisas / MedicalCheckUps, master trainee document types & categories (documentation) |
| `cms_tmm_trainee_document_ticketings` | trainee_flights, tickets, master airlines/airports (documentation departures) |
| `cms_masters` | master_document_submission_statuses (Submitted / Pending) |

---

## 🎨 Visual Design System

All dashboards share the same visual language: **gradient stat cards** with a large
number, label, and a faded oversized Font Awesome icon; white content **cards** with
subtle shadows; hover lift effects (`translateY(-3..5px)`).

**Gradient palette used across dashboards:**

| Gradient | Hex stops | Typical meaning |
|----------|-----------|-----------------|
| 💜 Purple | `#667eea → #764ba2` | Primary / totals |
| 🌸 Pink-red | `#f093fb → #f5576c` | Pending / attention |
| 💙 Blue-cyan | `#4facfe → #00f2fe` | Success / approved / active |
| 🌊 Teal | `#2193b0 → #6dd5ed` | Secondary totals (management) |
| 🔥 Orange-magenta | `#ee0979 → #ff6a00` | Organizations (management) |
| 💚 Green-mint | `#43e97b → #38f9d7` | Completed (training) |

**Styling approach differs by template** (historical drift):
- `admin`, `management`, `recruitment`, `lpk` → inline `<style>` blocks at the bottom of each `.ctp`
- `training`, `documentation` → inline `style=""` attributes on elements

Grid layout uses Bootstrap-style classes (`row`, `col-md-*`); icons are Font Awesome
(`fa` in most templates, `fas` in training/documentation).

---

## 🛡 Fallbacks & Error Handling

| Scenario | Behavior |
|----------|----------|
| DB/table unreachable in any stat helper | Count shows `0`, list shows empty state — **no exception surfaces** |
| User has no recognized role | Generic `index.ctp` with zeroed stats |
| LPK user without `institution_id` | Flash error + "No Institution" zeroed dashboard |
| Institution lookup fails | Header shows "Unknown Institution" |
| Empty recent lists | Friendly empty states (inbox icon / muted text) instead of empty tables |
| `processFlow` with invalid `?lang=` | Ignored — only `ind`, `eng`, `jpn` are accepted |

The fail-soft design means the dashboard **always renders**, which is intentional for a
multi-database system where individual connections may be down. The trade-off: a
misconfigured connection silently shows `0` instead of an error (check
`logs/error.log` if numbers look wrong).

---

## ➕ How to Add a New Role Dashboard

Follow these 5 steps (example: a `finance` role):

**1. Add the role check in `index()`** — position determines priority:

```php
if (in_array('finance', $roles)) {
    return $this->financeDashboard();
}
```

**2. Create the dashboard method** in `DashboardController`:

```php
protected function financeDashboard()
{
    $this->viewBuilder()->setTemplate('finance_dashboard');

    $stats = [
        'totalInvoices'   => $this->getTotalCount('Invoices', 'cms_tmm_finance'),
        'unpaidInvoices'  => $this->getCountByCondition('Invoices', 'cms_tmm_finance',
            ['status' => 'unpaid']),
    ];
    $recentInvoices = $this->getRecentRecords('Invoices', 'cms_tmm_finance', 10);

    $this->set(compact('stats', 'recentInvoices'));
}
```

**3. Create the template** `src/Template/Dashboard/finance_dashboard.ctp` — copy
`recruitment_dashboard.ctp` as the structural starting point (stat cards row → recent
table card → quick actions) and reuse the shared gradient palette.

**4. Ensure the role exists** in the `roles` table (`cms_authentication_authorization`
database) and is assigned to users via `roles_users`. The login flow picks it up
automatically through `role_names`.

**5. Verify authorization** — `isAuthorized()` already allows all authenticated users on
`Dashboard`, so no change needed unless the new dashboard links to controllers the role
can't access (configure those in the target controllers / `AppController`).

✅ **Checklist:**
- [ ] Role check placed at the correct priority in `index()`
- [ ] All stat queries go through the fail-soft helpers (never query directly)
- [ ] Template has empty states for all lists
- [ ] Quick-action links point to controllers the role is authorized for
- [ ] Tested with a user holding *only* the new role, and one holding multiple roles

---

## 🚧 Known Limitations & Roadmap

| # | Item | Status | Notes |
|---|------|--------|-------|
| 1 | ~~Management charts~~ | ✅ Implemented (2026-08-06) | `getTraineesByStatus()` + new `getCandidatesByInstitution()` feed pure-CSS bar charts; `getCandidatesByMonth()` activates automatically if a `created` column is ever added to `candidates` |
| 2 | ~~Admin "Recent Activity" panel~~ | ✅ Implemented | Recent Users / Candidates / Trainees feeds render; helpers now fall back to `id DESC` ordering when a table has no `created` column and tolerate already-configured registry aliases |
| 3 | ~~`getTotalCount()` ignores its `$connection` parameter~~ | ✅ Fixed | All count/recent helpers route through `locateTable()` which honours the connection |
| 9 | Post-login landing page | ✅ Changed (2026-08-06) | Login now lands on `/dashboard/process-flow` (system documentation with role-highlighted pipeline stages and jump links); the role dashboard remains at `/` |
| 4 | Admin `totalOrganizations` stat computed but unused | ⚠️ Minor | Either display it or drop the query |
| 5 | Duplicated CSS across templates | 🔧 Tech debt | The stat-card/card styles are repeated in 4 templates; candidates for extraction into `webroot/css` (see `TEMPLATE_IMPROVEMENTS.css`) |
| 6 | Status filter deep-links (`?status=...`) | ⚠️ Verify | Training card links pass a `status` query param — the target index actions must implement that filter for the links to actually filter |
| 8 | ~~Documentation dashboard queried nonexistent `ApprenticeDocuments` model~~ | ✅ Fixed | Rewritten to use the real `ApprenticeSubmissionDocuments` / record / flight models (previously always showed 0) |
| 7 | `getInstitutionName()` only supports `vocational_training` | 🔲 By design (for now) | Other institution types resolve to "Unknown Institution" |

---

## 📚 Related Files & Documentation

**Code**
- `src/Controller/DashboardController.php` — routing + all dashboard logic
- `src/Template/Dashboard/` — all seven templates (+ `process_flow.ctp`)
- `src/Controller/AppController.php` — `hasRole()`, current user handling
- `src/Controller/UsersController.php` — login populates `role_names`
- `config/routes.php` — `/` root route (line 75)

**Related dashboards (separate systems, not role-routed):**
- `src/Controller/Admin/StakeholderDashboardController.php` — admin stakeholder analytics (`/admin/stakeholder-dashboard`)
- `src/Controller/CandidatesController.php::dashboard` — candidate-specific dashboard (`/candidates/dashboard`)
- `ApprenticeDocumentManagementDashboards` / `CandidateDocumentManagementDashboardDetails` — document management dashboard tables

**Docs**
- `AUTHORIZATION_SYSTEM_COMPLETE.md` — full role/permission system
- `AUTHORIZATION_FLOW_DIAGRAMS.md` — auth flow diagrams
- `TEST_CREDENTIALS_GUIDE.md` / `QUICK_REFERENCE_TEST_CREDENTIALS.md` — test users per role
- `PROCESS_FLOW_COMPLETE_GUIDE.md` — the process-flow help system linked from dashboards
- `DATABASE_ASSOCIATIONS_REFERENCE.md` — cross-database model associations

---

*Generated for the TMM Apprentice Management System — CakePHP 3.x, multi-database CMS architecture.*
