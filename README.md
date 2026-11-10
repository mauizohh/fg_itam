# FG IT Asset Management System (FG ITAM)

> **Enterprise IT Asset Lifecycle, Custody Tracking & Governance Platform**  
> Developed for **FG** to fulfill all 50-page requirements of the **FG ITAM TECHNICAL PROPOSAL** and natively bridge with the **FG IT HELPDESK SYSTEM (`fg-ithd`)**.

---

## 1. Executive Summary & Overview

**FG IT Asset Management (FG ITAM)** provides complete visibility and lifecycle governance over organizational hardware, peripherals, software licenses, procurement orders, maintenance events, and physical inventory audits.

### Key Highlights
- **Unified Asset Lifecycle**: Tracks assets from procurement (PO) -> tagging -> assignment (custody) -> transfer/loan -> repair & maintenance -> decommission/disposal.
- **Cross-System Helpdesk Integration**: Natively integrated with `FG IT HELPDESK SYSTEM` (`fg-ithd`) via the standardized **Asset ID format** (`IT-{DEV}-{YEAR}-{NUMBER 5 digits}`).
- **Interactive Barcode/QR Audit Station**: Live physical verification with 1-click condition reporting (Found, Wrong Location, Damaged, Not Found) and automated discrepancy exception tracking.
- **Enterprise Reporting**: 6 one-click PDF reports powered by DomPDF with official FG Crimson header styling.
- **Automated Audit Trail**: Immutable logging of all checkout, checkin, transfer, repair, status change, and system configuration events.

---

## 2. Technical Stack Compliance

The system strictly implements the specifications defined in `TECH_STACK.txt`:

| Layer | Technology | Specification / Version |
|---|---|---|
| **Backend Framework** | Laravel | **v12.x / v11.x** (PHP 8.4 Support) |
| **PHP Runtime** | PHP | **v8.4.26** (Laragon Runtime) |
| **Frontend Reactive** | Laravel Livewire | **v3.x** Full-page and reactive components |
| **Component Library** | WireUI | **v2.x** Form inputs, modals, notifications, dialogs |
| **CSS & Design** | Tailwind CSS | **v3.4** Custom themed with Crimson palette |
| **Build Tooling** | Vite | **v5.x** Hot-reload and asset bundling |
| **PDF Generation** | Barryvdh DomPDF | **v3.1** Print-ready enterprise reports |
| **Database** | MySQL | **v8.0** Unified database `admidio` with prefix `fg_itam_` |
| **Local Environment** | Laragon | Apache 2.4, Virtual Host `http://fg-itam.test` |

---

## 3. Theme & Brand Design Compliance

The user interface adheres to the visual standards defined in `THEME_SPECIFICATION.txt`:

- **Primary Brand Red**: `#a92d31` (`--primary-600`)
- **Dark Red Accent**: `#8f2428` (`--primary-700`)
- **Light Red Surface**: `#fbf5f5` (`--primary-50`)
- **Border / Divider Red**: `#f5e4e5` (`--primary-100`)
- **Typography**:
  - Headings: `Poppins`, sans-serif (weights 600, 700)
  - Body & UI: `Montserrat`, sans-serif (weights 400, 500, 600)
- **Layout Architecture**:
  - Fixed Topbar: 70px height, Crimson branding, unified search bar, user profile, and instant Helpdesk quick link.
  - Collapsible Sidebar: 238px width, 10 primary navigation sections with active indicator bars and SVG iconography.
  - Status Pills: Standardized color coding (Green: Available, Blue: In Use, Amber: In Repair, Gray: Retired, Red: Missing/Exception).
  - Floating Quick Chat / Bridge Widget: Bottom-right interactive drawer for quick asset lookups and Helpdesk ticket linking.

---

## 4. Asset ID Standard & Cross-System Integration

### Standard ID Format
$$\text{IT}-\text{\{DEV\}}-\text{\{YEAR\}}-\text{\{5-DIGIT SEQUENCE\}}$$

- **Prefix**: `IT-` (FG Information Technology)
- **Device Code**: 3-letter uppercase category code:
  - `DSK`: Desktop Workstations
  - `LAP`: Laptops / Notebooks
  - `MON`: Monitors & Displays
  - `SRV`: Physical & Virtual Servers
  - `NET`: Network Routers, Switches, Firewalls
  - `PRN`: Network Printers & Multi-function Units
  - `UPS`: Uninterruptible Power Supplies
  - `PER`: General Peripherals & Accessories
- **Year**: 4-digit acquisition year (e.g. `2026`)
- **Series Number**: 5-digit zero-padded sequence counter (e.g. `00001` through `99999`)
- **Examples**:
  - `IT-DSK-2026-00001` (Dell OptiPlex 7000 Desktop)
  - `IT-LAP-2026-00002` (Dell Latitude 7450 Laptop)
  - `IT-NET-2026-00005` (Cisco Catalyst 9300 Switch)

### Helpdesk Bridge (`fg-ithd`)
1. **Shared Database Connection**: Both systems reside on MySQL database `admidio`.
2. **Synchronized Tables**:
   - `fg_itam_assets`: Full enterprise asset register with purchase, depreciation, warranty, and custody data.
   - `fg_helpdesk_assets`: Helpdesk asset table utilized by `C:\Users\Mauizoh\fg-ithd` when logging and resolving trouble tickets.
3. **Bi-Directional Event Synchronization**:
   - Every time an asset is created, updated, or re-assigned in FG ITAM, an automated model event hook updates or creates the corresponding row in `fg_helpdesk_assets` (`asset_id`, `name`, `category`, `serial_number`, `model`, `location`, `assigned_to`, `status`, `purchase`, `warranty`).
   - The Topbar and Quick Chat bridge provide direct links to open tickets associated with the Asset ID in `fg-ithd`.

---

## 5. Screen Inventory (All 32 UI Screens Implemented)

Every screen outlined in the 50-page Technical Proposal is fully implemented with real reactive Livewire components:

| UI Code | Screen Name | Route | Livewire Component | Description |
|---|---|---|---|---|
| **UI 01** | Dashboard | `/dashboard` | `Dashboard` | KPI metric cards, asset status doughnut, department custody chart, warranty alerts, recent activities |
| **UI 02** | All Assets | `/assets` | `Assets\AllAssets` | Filterable data table, pagination, export options, inline status pills, and **Add Asset Modal** |
| **UI 03** | Available Assets | `/assets/available` | `Assets\AvailableAssets` | Pool of unassigned, deployable hardware ready for assignment |
| **UI 04** | Assigned Assets | `/assets/assigned` | `Assets\AssignedAssets` | Deployed hardware actively checked out to staff members |
| **UI 05** | Assets In Repair | `/assets/in-repair` | `Assets\AssetsInRepair` | Hardware currently in diagnostic or vendor maintenance |
| **UI 06** | Retired Assets | `/assets/retired` | `Assets\RetiredAssets` | End-of-life and decommissioned assets with **Decommission Modal** |
| **UI 07** | Employees Directory | `/people/employees` | `People\Employees` | Staff directory with department tags, contact info, and active custody counters |
| **UI 08** | Departments Directory | `/people/departments` | `People\Departments` | Departmental breakdown with asset distribution and head of department |
| **UI 09** | Assign / Check-Out | `/transactions/assign` | `Transactions\AssignAsset` | Asset checkout form with employee selection, expected return date, and notes |
| **UI 10** | Return / Check-In | `/transactions/return` | `Transactions\ReturnAsset` | Hardware return with **3-Point Checklist** (Power-on test, Charger returned, Data wipe confirmation) |
| **UI 11** | Transfer Asset | `/transactions/transfer` | `Transactions\TransferAsset` | Inter-departmental and peer-to-peer custody transfer workflow |
| **UI 12** | Loan Asset | `/transactions/loan` | `Transactions\LoanAsset` | Temporary equipment loan tracking with due-date alerts |
| **UI 13** | Suppliers Directory | `/procurement/suppliers` | `Procurement\Suppliers` | Authorized vendors, contract terms, support phone/emails, and rating |
| **UI 14** | Purchases Register | `/procurement/purchases` | `Procurement\Purchases` | Capital expenditure log, invoices, purchase dates, and cost breakdown |
| **UI 15** | Purchase Orders | `/procurement/orders` | `Procurement\PurchaseOrders` | PO tracking with status workflow (Draft, Approved, Received, Cancelled) |
| **UI 16** | Repairs & Maintenance | `/maintenance/repairs` | `Maintenance\Repairs` | Work orders, vendor maintenance tickets, repair cost tracking, and turnaround times |
| **UI 17** | Warranty Tracking | `/maintenance/warranty` | `Maintenance\Warranty` | Active warranties, upcoming expirations (< 90 days), and claim history |
| **UI 18** | Software Catalog | `/software/catalog` | `Software\SoftwareCatalog` | Managed software titles, vendors, versions, and deployment footprint |
| **UI 19** | Licenses Register | `/software/licenses` | `Software\Licenses` | Seat allocation, license keys, utilization meters, and compliance ratios |
| **UI 20** | License Renewals | `/software/renewals` | `Software\Renewals` | Subscription renewal calendar, cost projections, and cancellation flags |
| **UI 21** | Physical Audit Station | `/audits/new` | `Audits\NewAudit` | Barcode/QR input station with instant verification buttons (**Found**, **Wrong Location**, **Damaged**, **Not Found**) |
| **UI 22** | Audit History | `/audits/history` | `Audits\AuditHistory` | Historical audit logs with completeness percentage, surveyor name, and date |
| **UI 23** | Audit Exceptions | `/audits/exceptions` | `Audits\Exceptions` | Discrepancy management dashboard for resolving missing or damaged hardware |
| **UI 24** | Reports Hub | `/reports` | `Reports\ReportsIndex` | Centralized reporting console with instant filter previews and 6 DomPDF downloads |
| **UI 25** | Asset Categories | `/admin/categories` | `Administration\AssetCategories` | Category management with 3-letter device codes, depreciation rates, and icon assignments |
| **UI 26** | Locations & Rooms | `/admin/locations` | `Administration\Locations` | Multi-tier campus, building, floor, and room hierarchy |
| **UI 27** | System Users | `/admin/users` | `Administration\Users` | Operator management with status toggle, role assignment, and last login tracking |
| **UI 28** | Roles & Permissions | `/admin/roles` | `Administration\Roles` | Role-based permission matrix (Super Admin, IT Asset Manager, Field Auditor, Read Only) |
| **UI 29** | System Settings | `/admin/settings` | `Administration\Settings` | Asset ID prefix configuration, warranty warning thresholds, and retention policies |
| **UI 30** | System Audit Logs | `/admin/audit-logs` | `Administration\AuditLogs` | Immutable chronological event log with IP addresses and user actions |
| **UI 31** | Asset Detail View | `/assets/{id}` | `Assets\AssetDetail` | Comprehensive lifecycle dossier, hardware specs, current custodian, repair history, and custody timeline |
| **UI 32** | Employee Custody View | `/people/employees/{id}` | `People\EmployeeDetail` | Staff profile displaying all assigned equipment with quick-return action buttons |

---

## 6. Enterprise PDF Reports (DomPDF)

The system includes 6 print-ready PDF export endpoints generated via `App\Http\Controllers\ReportController`:

1. **Complete Asset Register**: `/reports/pdf/complete-register`
2. **Department Inventory Summary**: `/reports/pdf/by-department`
3. **Asset Custody & Assignment History**: `/reports/pdf/assignment-history`
4. **Warranty Expiration Forecast**: `/reports/pdf/warranty-expiry`
5. **Physical Audit Discrepancies & Exceptions**: `/reports/pdf/audit-exceptions`
6. **Software License Compliance & Utilization**: `/reports/pdf/license-utilization`

All reports feature the official FG Crimson banner, table formatting, confidentiality notices, and generation timestamps.

---

## 7. RESTful API Endpoints

FG ITAM exposes JSON endpoints under `/api/v1` for external integrations and barcode scanning hardware:

- `GET /api/v1/assets`: Paginated asset list with status and category filtering.
- `GET /api/v1/assets/{id}`: Full detail of a single asset including assignment, location, and warranty.
- `GET /api/v1/assets/lookup?q={query}`: Fast lookup by Asset ID, serial number, or barcode.
- `GET /api/v1/stats`: Real-time system statistics for external monitoring dashboards.

---

## 8. Database Architecture

All tables use MySQL InnoDB with `utf8mb4_unicode_ci` and the `fg_itam_` prefix:

1. `fg_itam_categories`: Device classes, 3-letter codes, and depreciation periods.
2. `fg_itam_locations`: Campuses, buildings, floors, and room numbers.
3. `fg_itam_departments`: Corporate departments and cost centers.
4. `fg_itam_employees`: Staff members and custodians.
5. `fg_itam_suppliers`: Hardware vendors and warranty service providers.
6. `fg_itam_purchases`: Capital asset purchases and invoice numbers.
7. `fg_itam_purchase_orders`: Procurement requisition and receiving workflow.
8. `fg_itam_assets`: Central hardware asset register.
9. `fg_itam_repairs`: Maintenance work orders and service records.
10. `fg_itam_software`: Licensed application catalog.
11. `fg_itam_licenses`: License keys, volume agreements, and seat counts.
12. `fg_itam_renewals`: License and support contract renewal tracking.
13. `fg_itam_audits`: Physical inventory audit sessions.
14. `fg_itam_audit_exceptions`: Audit discrepancies and resolution tracking.
15. `fg_itam_transactions`: Checkout, checkin, transfer, and loan event logs.
16. `fg_itam_audit_logs`: System-level security and operator activity audit trail.
17. `fg_itam_settings`: System preferences and configuration flags.
18. `fg_helpdesk_assets`: Synchronized bridge table for `fg-ithd`.

---

## 9. Running & Developing the Application

### Option A: Laragon (Recommended)
A directory junction has been created in `C:\laragon\www\fg-itam` and an Apache virtual host configuration has been registered:
1. Open the **Laragon** control panel.
2. Click **Start All** (starts Apache and MySQL).
3. Access the application in your browser:
   ```
   http://fg-itam.test
   ```
   *(Or alternatively via `http://localhost/fg-itam`)*

### Option B: Artisan Development Server
If you prefer running via the command line:
```bash
cd C:\Users\Mauizoh\fg-itam
C:\laragon\bin\php\php-8.4.26-Win32-vs17-x64\php.exe artisan serve
```
Then visit:
```
http://127.0.0.1:8000
```

### Frontend Asset Compilation
The production assets are already pre-compiled in `public/build`. To run the Vite hot-reloading development server:
```bash
cd C:\Users\Mauizoh\fg-itam
npm run dev
```
To re-compile production assets:
```bash
npm run build
```

---

*FG IT Asset Management System — Developed in accordance with the FG ITAM Technical Proposal & Theme Specifications.*
