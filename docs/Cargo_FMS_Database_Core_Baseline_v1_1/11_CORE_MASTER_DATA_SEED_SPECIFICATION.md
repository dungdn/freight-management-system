# Cargo FMS — Core & Master Data Seed Specification

**Document Version:** 1.0  
**Baseline Reference:** `Cargo_FMS_Database_Core_Baseline_v1_1`  
**Execution Environment:** Laravel 12 / MySQL 8.x / Seeders

---

## 1. Overview & Tracing Architecture

This document specifies the initial foundational seed dataset populated into the database. It establishes a complete, traceable baseline for local development, automated testing, and verification of business rules.

### Seeder Class Hierarchy

```text
DatabaseSeeder
 ├── GlobalReferenceSeeder      (Currencies, Countries, Transport Modes, Package Types, Container Types, Charge Categories, Service Types)
 ├── NetworkGeographySeeder     (Locations, Ports, Airports)
 ├── OrganizationAndUserSeeder  (Company, Branches, Departments, Spatie Roles, Seed Users, Access Grants)
 └── CommercialMasterSeeder     (Payment Terms, Tax Codes, Charge Codes, FX Rates, Numbering Sequences, Sample Partners)
```

---

## 2. Seed Dataset Details

### 2.1 Global References (`GlobalReferenceSeeder`)
- **Currencies:** `VND` (Base, minor: 0), `USD` (minor: 2), `EUR` (minor: 2), `JPY` (minor: 0), `CNY` (minor: 2), `SGD` (minor: 2).
- **Countries:** `VN` (Vietnam), `US` (United States), `CN` (China), `SG` (Singapore), `JP` (Japan), `KR` (South Korea), `TH` (Thailand), `MY` (Malaysia), `DE` (Germany), `GB` (United Kingdom).
- **Transport Modes:** `OCEAN`, `AIR`, `ROAD`, `RAIL`, `MULTIMODAL`.
- **Package Types:** `CTN` (Carton/Box), `PLT` (Pallet), `DRUM`, `BAG`, `CRATE`, `ROLL`, `PKG`, `WOODEN_CASE`.
- **Container Types (ISO 6346):** `20GP` (22G1), `40GP` (42G1), `40HC` (45G1), `45HC`, `20RF` (Reefer), `40RF` (Reefer), `20OT` (Open Top), `40OT`, `20FR` (Flat Rack), `40FR`.
- **Charge Categories:** `FREIGHT`, `ORIGIN_LOCAL`, `DEST_LOCAL`, `CUSTOMS`, `INLAND`, `WAREHOUSE`, `DOCUMENTATION`, `OTHER`.
- **Service Types:**
  - `OCEAN_FCL_EXP` (Ocean FCL Export, mode: OCEAN)
  - `OCEAN_FCL_IMP` (Ocean FCL Import, mode: OCEAN)
  - `OCEAN_LCL_EXP` (Ocean LCL Export, mode: OCEAN)
  - `OCEAN_LCL_IMP` (Ocean LCL Import, mode: OCEAN)
  - `AIR_EXP` (Air Freight Export, mode: AIR)
  - `AIR_IMP` (Air Freight Import, mode: AIR)
  - `TRUCKING_DOM` (Domestic Inland Trucking, mode: ROAD)
  - `CUSTOMS_SVC` (Customs Brokerage, cross-modal)

### 2.2 Network Geography (`NetworkGeographySeeder`)
- **Locations (Cities):** `SGN` (Ho Chi Minh City), `HPH` (Hai Phong), `HAN` (Ha Noi), `DAD` (Da Nang), `SIN` (Singapore), `LAX` (Los Angeles), `SHA` (Shanghai).
- **Ports (UN/LOCODE):**
  - `VNSGN`: Cat Lai / Ho Chi Minh Port
  - `VNHPH`: Hai Phong Port
  - `VNDAD`: Da Nang Port
  - `VNVUT`: Cai Mep / Thi Vai Deepwater Port
  - `SGSIN`: Port of Singapore
  - `USLAX`: Port of Los Angeles
  - `USLGB`: Port of Long Beach
  - `CNSHA`: Port of Shanghai
  - `CNNBO`: Port of Ningbo-Zhoushan
- **Airports (IATA / ICAO):**
  - `SGN` / `VVTS`: Tan Son Nhat International Airport
  - `HAN` / `VVNB`: Noi Bai International Airport
  - `DAD` / `VVDN`: Da Nang International Airport
  - `SIN` / `WSSS`: Singapore Changi Airport
  - `LAX` / `KLAX`: Los Angeles International Airport
  - `PVG` / `ZSPD`: Shanghai Pudong International Airport

### 2.3 Organization & Security Context (`OrganizationAndUserSeeder`)
- **Company:**
  - Code: `CARGO_VN`
  - Legal Name: `Cargo Logistics Vietnam Joint Stock Company`
  - Base Currency: `VND`
  - Timezone: `Asia/Ho_Chi_Minh`
  - Tax Code: `0102030405`
- **Branches:**
  - `SGN_HQ`: Ho Chi Minh Head Office (`is_head_office: true`)
  - `HPH_BR`: Hai Phong Branch Office
  - `HAN_BR`: Ha Noi Representative Office
- **Departments:** `BOD` (Board of Directors), `SALES`, `OPS`, `DOCS`, `ACC`, `LOG`.
- **RBAC Roles (Spatie Teams scoped to `company_id = 1`):**
  - `Super Admin`, `Operations Manager`, `Sales Executive`, `Documentation Specialist`, `Accountant`.
- **Seed User Accounts (Password for all: `password`):**

| Email | Full Name | Primary Role | Access Level | Branch Access |
|---|---|---|---|---|
| `admin@cargofms.com` | Administrator | Super Admin | ADMIN | SGN_HQ, HPH_BR |
| `sales@cargofms.com` | Nguyen Van Sales | Sales Executive | MEMBER | SGN_HQ |
| `ops@cargofms.com` | Tran Thi Ops | Operations Manager | MEMBER | SGN_HQ, HPH_BR |
| `docs@cargofms.com` | Le Van Docs | Documentation Specialist | MEMBER | SGN_HQ |
| `acc@cargofms.com` | Pham Thi Accountant | Accountant | MEMBER | SGN_HQ |

### 2.4 Commercial Masters & Numbering (`CommercialMasterSeeder`)
- **Payment Terms:** `COD` (0 days), `PREPAID` (0 days), `NET7` (7 days), `NET15` (15 days), `NET30` (30 days).
- **Tax Codes:** `VAT0` (0%), `VAT5` (5%), `VAT8` (8%), `VAT10` (10%), `EXEMPT` (0%).
- **Standard Charge Codes:** `OFR` (Ocean Freight), `AFR` (Air Freight), `THC` (Terminal Handling), `DOC` (Bill of Lading fee), `SEAL` (Seal fee), `TELEX` (Telex release), `CFS` (CFS charge), `CIC` (Container imbalance), `TRUCK` (Inland haulage), `CC` (Customs clearance), `DO` (Delivery order fee).
- **Initial Exchange Rates:**
  - `USD -> VND`: 25,450.0000000000 (Accounting rate, VCB)
  - `EUR -> VND`: 26,800.0000000000 (Accounting rate, VCB)
- **Yearly Numbering Sequences (Period: `2026`, Scope: Company-wide `branch_scope_id = 0`):**

| Document Type | Prefix | Padding | Sample Generated Number |
|---|---|---|---|
| `INQUIRY` | `INQ26-` | 5 | `INQ26-00001` |
| `QUOTATION` | `QT26-` | 5 | `QT26-00001` |
| `SALES_ORDER` | `SO26-` | 5 | `SO26-00001` |
| `SHIPMENT` | `SH26-` | 6 | `SH26-000001` |
| `BOOKING` | `BK26-` | 5 | `BK26-00001` |
| `HBL` | `HBL26-` | 6 | `HBL26-000001` |
| `MBL` | `MBL26-` | 6 | `MBL26-000001` |
| `DEBIT_NOTE` | `DN26-` | 5 | `DN26-00001` |
| `CREDIT_NOTE` | `CN26-` | 5 | `CN26-00001` |
| `INVOICE` | `INV26-` | 6 | `INV26-000001` |

### 2.5 Sample Business Partners
- `CUST_SAMSUNG`: Samsung Electronics Vietnam (Roles: `CUSTOMER`, `SHIPPER`, Terms: `NET30`, Limit: \$100,000 USD).
- `CUST_VINFAST`: VinFast Trading & Production LLC (Roles: `CUSTOMER`, `SHIPPER`, Terms: `NET30`, Limit: 2,000,000,000 VND).
- `LINE_ONE`: Ocean Network Express (ONE) (Roles: `CARRIER`, `VENDOR`).
- `AGENT_KOREA`: Hankuk Global Logistics Korea (Roles: `AGENT`, `COLOADER`, `VENDOR`, Terms: `NET15`).

---

## 3. Traceability Matrix (Migrations to Seeds to Business Domain)

| Domain | Migration File | Seeder Class | Seed Verified in DB |
|---|---|---|:---:|
| Currencies | `...000001_create_currencies_table` | `GlobalReferenceSeeder` | ✅ |
| Countries | `...000002_create_countries_table` | `GlobalReferenceSeeder` | ✅ |
| Transport Modes | `...000003_create_transport_modes_table` | `GlobalReferenceSeeder` | ✅ |
| Package Types | `...000004_create_package_types_table` | `GlobalReferenceSeeder` | ✅ |
| Container Types | `...000005_create_container_types_table` | `GlobalReferenceSeeder` | ✅ |
| Charge Categories | `...000006_create_charge_categories_table` | `GlobalReferenceSeeder` | ✅ |
| Companies | `...000007_create_companies_table` | `OrganizationAndUserSeeder` | ✅ |
| Branches | `...000008_create_branches_table` | `OrganizationAndUserSeeder` | ✅ |
| Departments | `...000009_create_departments_table` | `OrganizationAndUserSeeder` | ✅ |
| Users Update | `...000001_update_users_table_for_cargo_fms` | `OrganizationAndUserSeeder` | ✅ |
| User Company Access | `...000002_create_user_company_access_table` | `OrganizationAndUserSeeder` | ✅ |
| User Branch Access | `...000003_create_user_branch_access_table` | `OrganizationAndUserSeeder` | ✅ |
| Spatie RBAC | `...034807_create_permission_tables` | `OrganizationAndUserSeeder` | ✅ |
| Locations | `...000004_create_locations_table` | `NetworkGeographySeeder` | ✅ |
| Ports | `...000005_create_ports_table` | `NetworkGeographySeeder` | ✅ |
| Airports | `...000006_create_airports_table` | `NetworkGeographySeeder` | ✅ |
| Payment Terms | `...000007_create_payment_terms_table` | `CommercialMasterSeeder` | ✅ |
| Tax Codes | `...000008_create_tax_codes_table` | `CommercialMasterSeeder` | ✅ |
| Business Partners | `...000009_create_business_partners_table` | `CommercialMasterSeeder` | ✅ |
| Partner Roles | `...000010_create_partner_roles_table` | `CommercialMasterSeeder` | ✅ |
| Partner Contacts | `...000011_create_partner_contacts_table` | `CommercialMasterSeeder` | ✅ |
| Partner Addresses | `...000012_create_partner_addresses_table` | `CommercialMasterSeeder` | ✅ |
| Credit Profiles | `...000013_create_partner_credit_profiles_table` | `CommercialMasterSeeder` | ✅ |
| Sales Assignments | `...000014_create_partner_sales_assignments_table` | `CommercialMasterSeeder` | ✅ |
| Service Types | `...000015_create_service_types_table` | `GlobalReferenceSeeder` | ✅ |
| Charge Codes | `...000016_create_charge_codes_table` | `CommercialMasterSeeder` | ✅ |
| Exchange Rates | `...000017_create_exchange_rates_table` | `CommercialMasterSeeder` | ✅ |
| Numbering Sequences | `...000018_create_numbering_sequences_table` | `CommercialMasterSeeder` | ✅ |
