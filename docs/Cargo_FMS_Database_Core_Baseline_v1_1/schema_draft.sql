-- Cargo FMS database starter draft
-- Scope: Core organization/master data + minimal Sales + Shipment foundations.
-- Not production-ready. Review MySQL version, tenancy rules, constraints and indexes before migrations.
-- Suggested baseline: MySQL 8.x / InnoDB / utf8mb4.

CREATE TABLE companies (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(30) NOT NULL,
  name VARCHAR(190) NOT NULL,
  base_currency_code CHAR(3) NOT NULL,
  timezone VARCHAR(64) NOT NULL DEFAULT 'Asia/Ho_Chi_Minh',
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_companies_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE branches (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id BIGINT UNSIGNED NOT NULL,
  code VARCHAR(30) NOT NULL,
  name VARCHAR(190) NOT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_branches_company_code (company_id, code),
  CONSTRAINT fk_branches_company FOREIGN KEY (company_id) REFERENCES companies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE departments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id BIGINT UNSIGNED NOT NULL,
  branch_id BIGINT UNSIGNED NULL,
  code VARCHAR(30) NOT NULL,
  name VARCHAR(190) NOT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_departments_company_code (company_id, code),
  KEY idx_departments_branch (branch_id),
  CONSTRAINT fk_departments_company FOREIGN KEY (company_id) REFERENCES companies(id),
  CONSTRAINT fk_departments_branch FOREIGN KEY (branch_id) REFERENCES branches(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE currencies (
  code CHAR(3) PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  decimal_places TINYINT UNSIGNED NOT NULL DEFAULT 2,
  is_active BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE countries (
  code CHAR(2) PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE locations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  country_code CHAR(2) NOT NULL,
  region VARCHAR(100) NULL,
  city VARCHAR(100) NULL,
  name VARCHAR(190) NULL,
  timezone VARCHAR(64) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  KEY idx_locations_country_city (country_code, city),
  CONSTRAINT fk_locations_country FOREIGN KEY (country_code) REFERENCES countries(code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE business_partners (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id BIGINT UNSIGNED NOT NULL,
  partner_code VARCHAR(40) NOT NULL,
  legal_name VARCHAR(190) NOT NULL,
  display_name VARCHAR(190) NULL,
  tax_number VARCHAR(60) NULL,
  default_currency_code CHAR(3) NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_partner_company_code (company_id, partner_code),
  KEY idx_partner_company_name (company_id, legal_name),
  CONSTRAINT fk_partners_company FOREIGN KEY (company_id) REFERENCES companies(id),
  CONSTRAINT fk_partners_currency FOREIGN KEY (default_currency_code) REFERENCES currencies(code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE partner_roles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_partner_id BIGINT UNSIGNED NOT NULL,
  role_code VARCHAR(30) NOT NULL,
  created_at TIMESTAMP NULL,
  UNIQUE KEY uq_partner_role (business_partner_id, role_code),
  CONSTRAINT fk_partner_roles_partner FOREIGN KEY (business_partner_id) REFERENCES business_partners(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE partner_contacts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_partner_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NULL,
  phone VARCHAR(40) NULL,
  title VARCHAR(100) NULL,
  is_primary BOOLEAN NOT NULL DEFAULT FALSE,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  KEY idx_partner_contacts_partner (business_partner_id),
  CONSTRAINT fk_partner_contacts_partner FOREIGN KEY (business_partner_id) REFERENCES business_partners(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE partner_addresses (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_partner_id BIGINT UNSIGNED NOT NULL,
  address_type VARCHAR(30) NOT NULL DEFAULT 'GENERAL',
  address_line1 VARCHAR(190) NOT NULL,
  address_line2 VARCHAR(190) NULL,
  city VARCHAR(100) NULL,
  region VARCHAR(100) NULL,
  postal_code VARCHAR(30) NULL,
  country_code CHAR(2) NOT NULL,
  is_primary BOOLEAN NOT NULL DEFAULT FALSE,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  KEY idx_partner_addresses_partner (business_partner_id),
  CONSTRAINT fk_partner_addresses_partner FOREIGN KEY (business_partner_id) REFERENCES business_partners(id),
  CONSTRAINT fk_partner_addresses_country FOREIGN KEY (country_code) REFERENCES countries(code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE service_types (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(30) NOT NULL,
  name VARCHAR(100) NOT NULL,
  transport_mode VARCHAR(20) NOT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  UNIQUE KEY uq_service_types_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE charge_codes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id BIGINT UNSIGNED NULL,
  code VARCHAR(40) NOT NULL,
  name VARCHAR(150) NOT NULL,
  default_charge_type VARCHAR(10) NOT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  UNIQUE KEY uq_charge_code_company_code (company_id, code),
  CONSTRAINT fk_charge_codes_company FOREIGN KEY (company_id) REFERENCES companies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE sales_inquiries (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id BIGINT UNSIGNED NOT NULL,
  branch_id BIGINT UNSIGNED NOT NULL,
  inquiry_number VARCHAR(40) NOT NULL,
  business_partner_id BIGINT UNSIGNED NOT NULL,
  service_type_id BIGINT UNSIGNED NULL,
  direction VARCHAR(10) NOT NULL,
  transport_mode VARCHAR(20) NOT NULL,
  origin_location_id BIGINT UNSIGNED NULL,
  destination_location_id BIGINT UNSIGNED NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'OPEN',
  owner_user_id BIGINT UNSIGNED NULL,
  requested_ship_date DATE NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_inquiry_company_number (company_id, inquiry_number),
  KEY idx_inquiry_branch_status (company_id, branch_id, status),
  CONSTRAINT fk_inquiries_company FOREIGN KEY (company_id) REFERENCES companies(id),
  CONSTRAINT fk_inquiries_branch FOREIGN KEY (branch_id) REFERENCES branches(id),
  CONSTRAINT fk_inquiries_partner FOREIGN KEY (business_partner_id) REFERENCES business_partners(id),
  CONSTRAINT fk_inquiries_service FOREIGN KEY (service_type_id) REFERENCES service_types(id),
  CONSTRAINT fk_inquiries_origin FOREIGN KEY (origin_location_id) REFERENCES locations(id),
  CONSTRAINT fk_inquiries_destination FOREIGN KEY (destination_location_id) REFERENCES locations(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE quotations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id BIGINT UNSIGNED NOT NULL,
  branch_id BIGINT UNSIGNED NOT NULL,
  quotation_number VARCHAR(40) NOT NULL,
  inquiry_id BIGINT UNSIGNED NULL,
  business_partner_id BIGINT UNSIGNED NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'DRAFT',
  current_revision_number SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  valid_until DATE NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_quotation_company_number (company_id, quotation_number),
  KEY idx_quotation_partner_status (company_id, business_partner_id, status),
  CONSTRAINT fk_quotations_company FOREIGN KEY (company_id) REFERENCES companies(id),
  CONSTRAINT fk_quotations_branch FOREIGN KEY (branch_id) REFERENCES branches(id),
  CONSTRAINT fk_quotations_inquiry FOREIGN KEY (inquiry_id) REFERENCES sales_inquiries(id),
  CONSTRAINT fk_quotations_partner FOREIGN KEY (business_partner_id) REFERENCES business_partners(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE quotation_revisions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  quotation_id BIGINT UNSIGNED NOT NULL,
  revision_number SMALLINT UNSIGNED NOT NULL,
  currency_code CHAR(3) NOT NULL,
  exchange_rate DECIMAL(19,8) NULL,
  total_amount DECIMAL(19,4) NOT NULL DEFAULT 0,
  terms_snapshot JSON NULL,
  revision_status VARCHAR(20) NOT NULL DEFAULT 'DRAFT',
  created_at TIMESTAMP NULL,
  UNIQUE KEY uq_quotation_revision (quotation_id, revision_number),
  CONSTRAINT fk_quotation_revisions_quote FOREIGN KEY (quotation_id) REFERENCES quotations(id),
  CONSTRAINT fk_quotation_revisions_currency FOREIGN KEY (currency_code) REFERENCES currencies(code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE quotation_lines (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  quotation_revision_id BIGINT UNSIGNED NOT NULL,
  charge_code_id BIGINT UNSIGNED NULL,
  description VARCHAR(190) NOT NULL,
  quantity DECIMAL(19,4) NOT NULL DEFAULT 1,
  unit_price DECIMAL(19,4) NOT NULL DEFAULT 0,
  line_amount DECIMAL(19,4) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL,
  CONSTRAINT fk_quotation_lines_revision FOREIGN KEY (quotation_revision_id) REFERENCES quotation_revisions(id),
  CONSTRAINT fk_quotation_lines_charge FOREIGN KEY (charge_code_id) REFERENCES charge_codes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE sales_orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id BIGINT UNSIGNED NOT NULL,
  branch_id BIGINT UNSIGNED NOT NULL,
  sales_order_number VARCHAR(40) NOT NULL,
  quotation_id BIGINT UNSIGNED NOT NULL,
  business_partner_id BIGINT UNSIGNED NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'ACCEPTED',
  accepted_at DATETIME NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_sales_order_company_number (company_id, sales_order_number),
  CONSTRAINT fk_sales_orders_company FOREIGN KEY (company_id) REFERENCES companies(id),
  CONSTRAINT fk_sales_orders_branch FOREIGN KEY (branch_id) REFERENCES branches(id),
  CONSTRAINT fk_sales_orders_quote FOREIGN KEY (quotation_id) REFERENCES quotations(id),
  CONSTRAINT fk_sales_orders_partner FOREIGN KEY (business_partner_id) REFERENCES business_partners(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE shipments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id BIGINT UNSIGNED NOT NULL,
  branch_id BIGINT UNSIGNED NOT NULL,
  shipment_number VARCHAR(40) NOT NULL,
  sales_order_id BIGINT UNSIGNED NULL,
  direction VARCHAR(10) NOT NULL,
  transport_mode VARCHAR(20) NOT NULL,
  service_type_id BIGINT UNSIGNED NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'DRAFT',
  operational_status VARCHAR(30) NULL,
  opened_at DATETIME NULL,
  completed_at DATETIME NULL,
  closed_at DATETIME NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_shipment_company_number (company_id, shipment_number),
  KEY idx_shipment_company_branch_status (company_id, branch_id, status),
  CONSTRAINT fk_shipments_company FOREIGN KEY (company_id) REFERENCES companies(id),
  CONSTRAINT fk_shipments_branch FOREIGN KEY (branch_id) REFERENCES branches(id),
  CONSTRAINT fk_shipments_sales_order FOREIGN KEY (sales_order_id) REFERENCES sales_orders(id),
  CONSTRAINT fk_shipments_service FOREIGN KEY (service_type_id) REFERENCES service_types(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE shipment_parties (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  shipment_id BIGINT UNSIGNED NOT NULL,
  party_role VARCHAR(30) NOT NULL,
  business_partner_id BIGINT UNSIGNED NULL,
  name_snapshot VARCHAR(190) NOT NULL,
  address_snapshot TEXT NULL,
  contact_snapshot VARCHAR(190) NULL,
  created_at TIMESTAMP NULL,
  KEY idx_shipment_parties_shipment (shipment_id),
  CONSTRAINT fk_shipment_parties_shipment FOREIGN KEY (shipment_id) REFERENCES shipments(id),
  CONSTRAINT fk_shipment_parties_partner FOREIGN KEY (business_partner_id) REFERENCES business_partners(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE shipment_cargo_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  shipment_id BIGINT UNSIGNED NOT NULL,
  package_type_code VARCHAR(30) NULL,
  package_count DECIMAL(19,4) NULL,
  gross_weight_kg DECIMAL(19,4) NULL,
  volume_cbm DECIMAL(19,4) NULL,
  description TEXT NULL,
  marks_and_numbers TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  KEY idx_cargo_items_shipment (shipment_id),
  CONSTRAINT fk_cargo_items_shipment FOREIGN KEY (shipment_id) REFERENCES shipments(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE shipment_legs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  shipment_id BIGINT UNSIGNED NOT NULL,
  leg_sequence SMALLINT UNSIGNED NOT NULL,
  transport_mode VARCHAR(20) NOT NULL,
  origin_location_id BIGINT UNSIGNED NULL,
  destination_location_id BIGINT UNSIGNED NULL,
  planned_departure DATETIME NULL,
  actual_departure DATETIME NULL,
  planned_arrival DATETIME NULL,
  actual_arrival DATETIME NULL,
  carrier_partner_id BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_shipment_leg_sequence (shipment_id, leg_sequence),
  CONSTRAINT fk_shipment_legs_shipment FOREIGN KEY (shipment_id) REFERENCES shipments(id),
  CONSTRAINT fk_shipment_legs_origin FOREIGN KEY (origin_location_id) REFERENCES locations(id),
  CONSTRAINT fk_shipment_legs_destination FOREIGN KEY (destination_location_id) REFERENCES locations(id),
  CONSTRAINT fk_shipment_legs_carrier FOREIGN KEY (carrier_partner_id) REFERENCES business_partners(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE bookings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_id BIGINT UNSIGNED NOT NULL,
  booking_number VARCHAR(40) NOT NULL,
  carrier_partner_id BIGINT UNSIGNED NULL,
  booking_reference VARCHAR(100) NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'REQUESTED',
  confirmed_at DATETIME NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_booking_company_number (company_id, booking_number),
  CONSTRAINT fk_bookings_company FOREIGN KEY (company_id) REFERENCES companies(id),
  CONSTRAINT fk_bookings_carrier FOREIGN KEY (carrier_partner_id) REFERENCES business_partners(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE booking_shipment_links (
  booking_id BIGINT UNSIGNED NOT NULL,
  shipment_id BIGINT UNSIGNED NOT NULL,
  linked_at TIMESTAMP NULL,
  PRIMARY KEY (booking_id, shipment_id),
  CONSTRAINT fk_booking_links_booking FOREIGN KEY (booking_id) REFERENCES bookings(id),
  CONSTRAINT fk_booking_links_shipment FOREIGN KEY (shipment_id) REFERENCES shipments(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE shipment_events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  shipment_id BIGINT UNSIGNED NOT NULL,
  event_code VARCHAR(40) NOT NULL,
  planned_at DATETIME NULL,
  actual_at DATETIME NULL,
  event_source VARCHAR(30) NULL,
  notes TEXT NULL,
  corrected_event_id BIGINT UNSIGNED NULL,
  recorded_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NULL,
  KEY idx_shipment_events_timeline (shipment_id, actual_at, planned_at),
  KEY idx_shipment_events_code (shipment_id, event_code),
  CONSTRAINT fk_shipment_events_shipment FOREIGN KEY (shipment_id) REFERENCES shipments(id),
  CONSTRAINT fk_shipment_events_correction FOREIGN KEY (corrected_event_id) REFERENCES shipment_events(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Planned follow-up schema areas intentionally omitted from this draft:
-- document templates/revisions/files/approvals; charges and charge allocations;
-- debit/credit/commission notes; invoices, open items, receipts/payments/allocations;
-- journal entries/lines; bank reconciliation; assets and prepaid schedules; consol tables.
