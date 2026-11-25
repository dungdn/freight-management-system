-- Cargo FMS Core & Master Data schema reference (review draft)
-- Target: MySQL 8.x / InnoDB / utf8mb4. NOT production-certified.
-- Apply only after approving 07_CORE_DECISIONS_AND_OPEN_ITEMS.md.
-- RBAC tables intentionally omitted: use the selected package's canonical migrations.

CREATE TABLE currencies (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  code CHAR(3) NOT NULL,
  name VARCHAR(80) NOT NULL,
  minor_unit TINYINT UNSIGNED NOT NULL,
  symbol VARCHAR(8) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id), UNIQUE KEY uq_currencies_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE countries (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  iso2 CHAR(2) NOT NULL,
  iso3 CHAR(3) NOT NULL,
  name VARCHAR(120) NOT NULL,
  phone_code VARCHAR(8) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id), UNIQUE KEY uq_countries_iso2 (iso2), UNIQUE KEY uq_countries_iso3 (iso3), KEY ix_countries_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE transport_modes (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(20) NOT NULL,
  name VARCHAR(80) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id), UNIQUE KEY uq_transport_modes_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE charge_categories (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(30) NOT NULL,
  name VARCHAR(100) NOT NULL,
  financial_side VARCHAR(10) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id), UNIQUE KEY uq_charge_categories_code (code),
  CONSTRAINT chk_charge_categories_side CHECK (financial_side IN ('REVENUE','COST','BOTH'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE companies (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(20) NOT NULL,
  legal_name VARCHAR(200) NOT NULL,
  display_name VARCHAR(200) NULL,
  tax_code VARCHAR(40) NULL,
  base_currency_id BIGINT UNSIGNED NOT NULL,
  timezone VARCHAR(64) NOT NULL DEFAULT 'Asia/Ho_Chi_Minh',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id), UNIQUE KEY uq_companies_code (code), UNIQUE KEY uq_companies_id_id (id),
  KEY ix_companies_active_code (is_active, code),
  CONSTRAINT fk_companies_base_currency FOREIGN KEY (base_currency_id) REFERENCES currencies(id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE branches (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  code VARCHAR(20) NOT NULL,
  name VARCHAR(160) NOT NULL,
  timezone VARCHAR(64) NULL,
  is_head_office TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id), UNIQUE KEY uq_branches_company_code (company_id, code), UNIQUE KEY uq_branches_company_id (company_id, id),
  KEY ix_branches_company_active_name (company_id, is_active, name),
  CONSTRAINT fk_branches_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE departments (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  branch_id BIGINT UNSIGNED NULL,
  code VARCHAR(20) NOT NULL,
  name VARCHAR(120) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id), UNIQUE KEY uq_departments_company_code (company_id, code),
  KEY ix_departments_company_branch_active (company_id, branch_id, is_active),
  CONSTRAINT fk_departments_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT fk_departments_company_branch FOREIGN KEY (company_id, branch_id) REFERENCES branches(company_id, id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(160) NOT NULL,
  email VARCHAR(190) NOT NULL,
  email_verified_at DATETIME(6) NULL,
  password VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at DATETIME(6) NULL,
  created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id), UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE user_company_access (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  company_id BIGINT UNSIGNED NOT NULL,
  access_level VARCHAR(20) NOT NULL DEFAULT 'MEMBER',
  is_default TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id), UNIQUE KEY uq_user_company_access (user_id, company_id),
  KEY ix_user_company_access_company (company_id, is_active, user_id),
  CONSTRAINT fk_uca_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT fk_uca_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE user_branch_access (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  company_id BIGINT UNSIGNED NOT NULL,
  branch_id BIGINT UNSIGNED NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id), UNIQUE KEY uq_user_branch_access (user_id, branch_id),
  KEY ix_uba_company_branch_user (company_id, branch_id, user_id),
  CONSTRAINT fk_uba_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT fk_uba_company_branch FOREIGN KEY (company_id, branch_id) REFERENCES branches(company_id, id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE locations (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  country_id BIGINT UNSIGNED NOT NULL,
  parent_location_id BIGINT UNSIGNED NULL,
  location_type VARCHAR(20) NOT NULL,
  code VARCHAR(30) NULL,
  name VARCHAR(160) NOT NULL,
  timezone VARCHAR(64) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id), UNIQUE KEY uq_locations_country_type_code (country_id, location_type, code),
  KEY ix_locations_country_type_name (country_id, location_type, name), KEY ix_locations_parent (parent_location_id),
  CONSTRAINT fk_locations_country FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT fk_locations_parent FOREIGN KEY (parent_location_id) REFERENCES locations(id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE business_partners (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  partner_code VARCHAR(30) NOT NULL,
  legal_name VARCHAR(200) NOT NULL,
  display_name VARCHAR(200) NULL,
  partner_kind VARCHAR(20) NOT NULL,
  tax_code VARCHAR(40) NULL,
  registration_no VARCHAR(60) NULL,
  website VARCHAR(255) NULL,
  default_currency_id BIGINT UNSIGNED NULL,
  payment_term_id BIGINT UNSIGNED NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
  notes TEXT NULL,
  created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id), UNIQUE KEY uq_partners_company_code (company_id, partner_code), UNIQUE KEY uq_partners_company_id (company_id, id),
  KEY ix_partners_company_status_name (company_id, status, legal_name), KEY ix_partners_company_tax_code (company_id, tax_code),
  CONSTRAINT fk_partners_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT fk_partners_currency FOREIGN KEY (default_currency_id) REFERENCES currencies(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT chk_partner_kind CHECK (partner_kind IN ('ORGANIZATION','INDIVIDUAL'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE partner_roles (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  business_partner_id BIGINT UNSIGNED NOT NULL,
  role_code VARCHAR(30) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  effective_from DATE NULL,
  effective_to DATE NULL,
  PRIMARY KEY (id), UNIQUE KEY uq_partner_roles_partner_role (business_partner_id, role_code),
  KEY ix_partner_roles_company_role (company_id, role_code, is_active),
  CONSTRAINT fk_partner_roles_company_partner FOREIGN KEY (company_id, business_partner_id) REFERENCES business_partners(company_id, id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT chk_partner_role_dates CHECK (effective_to IS NULL OR effective_from IS NULL OR effective_to >= effective_from)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE partner_contacts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  business_partner_id BIGINT UNSIGNED NOT NULL,
  full_name VARCHAR(160) NOT NULL,
  job_title VARCHAR(100) NULL,
  email VARCHAR(190) NULL,
  phone VARCHAR(40) NULL,
  mobile VARCHAR(40) NULL,
  is_primary TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  notes VARCHAR(500) NULL,
  created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id), KEY ix_partner_contacts_partner_active (business_partner_id, is_active), KEY ix_partner_contacts_company_email (company_id, email),
  CONSTRAINT fk_partner_contacts_company_partner FOREIGN KEY (company_id, business_partner_id) REFERENCES business_partners(company_id, id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE partner_addresses (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  business_partner_id BIGINT UNSIGNED NOT NULL,
  address_type VARCHAR(30) NOT NULL,
  label VARCHAR(100) NULL,
  contact_name VARCHAR(160) NULL,
  phone VARCHAR(40) NULL,
  address_line1 VARCHAR(200) NOT NULL,
  address_line2 VARCHAR(200) NULL,
  ward VARCHAR(100) NULL,
  district VARCHAR(100) NULL,
  city VARCHAR(100) NULL,
  region VARCHAR(100) NULL,
  postal_code VARCHAR(20) NULL,
  country_id BIGINT UNSIGNED NOT NULL,
  is_default TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id), KEY ix_partner_addresses_partner_type (business_partner_id, address_type, is_active), KEY ix_partner_addresses_company_country_city (company_id, country_id, city),
  CONSTRAINT fk_partner_addresses_company_partner FOREIGN KEY (company_id, business_partner_id) REFERENCES business_partners(company_id, id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT fk_partner_addresses_country FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE service_types (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  transport_mode_id BIGINT UNSIGNED NULL,
  code VARCHAR(30) NOT NULL,
  name VARCHAR(100) NOT NULL,
  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id), UNIQUE KEY uq_service_types_code (code), KEY ix_service_types_mode_active (transport_mode_id, is_active),
  CONSTRAINT fk_service_types_mode FOREIGN KEY (transport_mode_id) REFERENCES transport_modes(id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE charge_codes (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  charge_category_id BIGINT UNSIGNED NOT NULL,
  code VARCHAR(30) NOT NULL,
  name VARCHAR(120) NOT NULL,
  default_unit VARCHAR(20) NULL,
  is_taxable TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  PRIMARY KEY (id), UNIQUE KEY uq_charge_codes_company_code (company_id, code), KEY ix_charge_codes_company_category_active (company_id, charge_category_id, is_active),
  CONSTRAINT fk_charge_codes_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT fk_charge_codes_category FOREIGN KEY (charge_category_id) REFERENCES charge_categories(id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Deliberately not included in this compact reference DDL:
-- exchange_rates, ports, airports, partner_credit_profiles, partner_sales_assignments,
-- payment_terms, tax_codes, package_types, container_types, numbering_sequences.
-- Their complete specifications are in 05_CORE_MASTER_DATA_DETAILED_DESIGN.md.
