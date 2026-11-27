<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommercialMasterSeeder extends Seeder
{
    public function run(): void
    {
        $companyId = DB::table('companies')->where('code', 'CARGO_VN')->value('id');
        $vndId = DB::table('currencies')->where('code', 'VND')->value('id');
        $usdId = DB::table('currencies')->where('code', 'USD')->value('id');
        $eurId = DB::table('currencies')->where('code', 'EUR')->value('id');
        $vnCountryId = DB::table('countries')->where('iso2', 'VN')->value('id');
        $krCountryId = DB::table('countries')->where('iso2', 'KR')->value('id');

        $adminUserId = User::where('email', 'admin@cargofms.com')->value('id');
        $salesUserId = User::where('email', 'sales@cargofms.com')->value('id');

        // 1. Payment Terms
        $terms = [
            ['code' => 'COD', 'name' => 'Cash On Delivery', 'due_days' => 0, 'description' => 'Payment due upon delivery'],
            ['code' => 'PREPAID', 'name' => 'Advance Payment / Prepaid', 'due_days' => 0, 'description' => 'Payment prior to release of documents'],
            ['code' => 'NET7', 'name' => 'Net 7 Days', 'due_days' => 7, 'description' => 'Payment due within 7 days of invoice'],
            ['code' => 'NET15', 'name' => 'Net 15 Days', 'due_days' => 15, 'description' => 'Payment due within 15 days of invoice'],
            ['code' => 'NET30', 'name' => 'Net 30 Days', 'due_days' => 30, 'description' => 'Payment due within 30 days of invoice'],
        ];

        foreach ($terms as $term) {
            DB::table('payment_terms')->updateOrInsert(
                ['company_id' => $companyId, 'code' => $term['code']],
                array_merge($term, [
                    'company_id' => $companyId,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $net30Id = DB::table('payment_terms')->where('company_id', $companyId)->where('code', 'NET30')->value('id');
        $net15Id = DB::table('payment_terms')->where('company_id', $companyId)->where('code', 'NET15')->value('id');

        // 2. Tax Codes
        $taxCodes = [
            ['code' => 'VAT0', 'name' => 'VAT 0% (International transport/export)', 'rate' => 0.0000, 'tax_type' => 'ZERO', 'effective_from' => '2026-01-01'],
            ['code' => 'VAT5', 'name' => 'VAT 5%', 'rate' => 5.0000, 'tax_type' => 'VAT', 'effective_from' => '2026-01-01'],
            ['code' => 'VAT8', 'name' => 'VAT 8% (Incentive rate)', 'rate' => 8.0000, 'tax_type' => 'VAT', 'effective_from' => '2026-01-01'],
            ['code' => 'VAT10', 'name' => 'VAT 10% (Standard rate)', 'rate' => 10.0000, 'tax_type' => 'VAT', 'effective_from' => '2026-01-01'],
            ['code' => 'EXEMPT', 'name' => 'VAT Exempt', 'rate' => 0.0000, 'tax_type' => 'EXEMPT', 'effective_from' => '2026-01-01'],
        ];

        foreach ($taxCodes as $tc) {
            DB::table('tax_codes')->updateOrInsert(
                ['company_id' => $companyId, 'code' => $tc['code'], 'effective_from' => $tc['effective_from']],
                array_merge($tc, [
                    'company_id' => $companyId,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $vat8Id = DB::table('tax_codes')->where('company_id', $companyId)->where('code', 'VAT8')->value('id');

        // 3. Charge Codes
        $freightCatId = DB::table('charge_categories')->where('code', 'FREIGHT')->value('id');
        $originCatId = DB::table('charge_categories')->where('code', 'ORIGIN_LOCAL')->value('id');
        $destCatId = DB::table('charge_categories')->where('code', 'DEST_LOCAL')->value('id');
        $customsCatId = DB::table('charge_categories')->where('code', 'CUSTOMS')->value('id');
        $inlandCatId = DB::table('charge_categories')->where('code', 'INLAND')->value('id');
        $warehouseCatId = DB::table('charge_categories')->where('code', 'WAREHOUSE')->value('id');
        $docsCatId = DB::table('charge_categories')->where('code', 'DOCUMENTATION')->value('id');

        $charges = [
            ['code' => 'OFR', 'name' => 'Ocean Freight', 'charge_category_id' => $freightCatId, 'default_unit' => 'CONT', 'tax_code_id' => null, 'is_taxable' => false],
            ['code' => 'AFR', 'name' => 'Air Freight', 'charge_category_id' => $freightCatId, 'default_unit' => 'KGS', 'tax_code_id' => null, 'is_taxable' => false],
            ['code' => 'THC', 'name' => 'Terminal Handling Charge', 'charge_category_id' => $originCatId, 'default_unit' => 'CONT', 'tax_code_id' => $vat8Id, 'is_taxable' => true],
            ['code' => 'DOC', 'name' => 'Bill of Lading / Documentation Fee', 'charge_category_id' => $docsCatId, 'default_unit' => 'BL', 'tax_code_id' => $vat8Id, 'is_taxable' => true],
            ['code' => 'SEAL', 'name' => 'Container Seal Fee', 'charge_category_id' => $originCatId, 'default_unit' => 'SET', 'tax_code_id' => $vat8Id, 'is_taxable' => true],
            ['code' => 'TELEX', 'name' => 'Telex Release Fee', 'charge_category_id' => $docsCatId, 'default_unit' => 'BL', 'tax_code_id' => $vat8Id, 'is_taxable' => true],
            ['code' => 'CFS', 'name' => 'Container Freight Station Charge', 'charge_category_id' => $warehouseCatId, 'default_unit' => 'CBM', 'tax_code_id' => $vat8Id, 'is_taxable' => true],
            ['code' => 'CIC', 'name' => 'Container Imbalance Charge', 'charge_category_id' => $originCatId, 'default_unit' => 'CONT', 'tax_code_id' => $vat8Id, 'is_taxable' => true],
            ['code' => 'TRUCK', 'name' => 'Inland Trucking & Transport Fee', 'charge_category_id' => $inlandCatId, 'default_unit' => 'SHIPMENT', 'tax_code_id' => $vat8Id, 'is_taxable' => true],
            ['code' => 'CC', 'name' => 'Customs Clearance Brokerage Fee', 'charge_category_id' => $customsCatId, 'default_unit' => 'SET', 'tax_code_id' => $vat8Id, 'is_taxable' => true],
            ['code' => 'DO', 'name' => 'Delivery Order Fee', 'charge_category_id' => $destCatId, 'default_unit' => 'BL', 'tax_code_id' => $vat8Id, 'is_taxable' => true],
        ];

        foreach ($charges as $ch) {
            DB::table('charge_codes')->updateOrInsert(
                ['company_id' => $companyId, 'code' => $ch['code']],
                array_merge($ch, [
                    'company_id' => $companyId,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // 4. Exchange Rates
        $rates = [
            [
                'from_currency_id' => $usdId,
                'to_currency_id' => $vndId,
                'rate_type' => 'ACCOUNTING',
                'rate' => '25450.0000000000',
                'effective_at' => '2026-01-01 00:00:00',
                'source' => 'VCB',
                'created_by' => $adminUserId,
            ],
            [
                'from_currency_id' => $eurId,
                'to_currency_id' => $vndId,
                'rate_type' => 'ACCOUNTING',
                'rate' => '26800.0000000000',
                'effective_at' => '2026-01-01 00:00:00',
                'source' => 'VCB',
                'created_by' => $adminUserId,
            ],
        ];

        foreach ($rates as $r) {
            DB::table('exchange_rates')->updateOrInsert(
                [
                    'company_id' => $companyId,
                    'from_currency_id' => $r['from_currency_id'],
                    'to_currency_id' => $r['to_currency_id'],
                    'rate_type' => $r['rate_type'],
                    'effective_at' => $r['effective_at'],
                ],
                array_merge($r, [
                    'company_id' => $companyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // 5. Numbering Sequences (Yearly reset: 2026)
        $docTypes = [
            ['document_type' => 'INQUIRY', 'prefix' => 'INQ26-', 'padding' => 5],
            ['document_type' => 'QUOTATION', 'prefix' => 'QT26-', 'padding' => 5],
            ['document_type' => 'SALES_ORDER', 'prefix' => 'SO26-', 'padding' => 5],
            ['document_type' => 'SHIPMENT', 'prefix' => 'SH26-', 'padding' => 6],
            ['document_type' => 'BOOKING', 'prefix' => 'BK26-', 'padding' => 5],
            ['document_type' => 'HBL', 'prefix' => 'HBL26-', 'padding' => 6],
            ['document_type' => 'MBL', 'prefix' => 'MBL26-', 'padding' => 6],
            ['document_type' => 'DEBIT_NOTE', 'prefix' => 'DN26-', 'padding' => 5],
            ['document_type' => 'CREDIT_NOTE', 'prefix' => 'CN26-', 'padding' => 5],
            ['document_type' => 'INVOICE', 'prefix' => 'INV26-', 'padding' => 6],
        ];

        foreach ($docTypes as $dt) {
            DB::table('numbering_sequences')->updateOrInsert(
                [
                    'company_id' => $companyId,
                    'branch_scope_id' => 0, // 0 = company-wide scope
                    'document_type' => $dt['document_type'],
                    'period_key' => '2026',
                ],
                [
                    'company_id' => $companyId,
                    'branch_scope_id' => 0,
                    'document_type' => $dt['document_type'],
                    'period_key' => '2026',
                    'prefix' => $dt['prefix'],
                    'next_number' => 1,
                    'padding' => $dt['padding'],
                    'reset_policy' => 'YEARLY',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 6. Business Partners (Sample customer, carrier, agent, vendor)
        $partners = [
            [
                'partner_code' => 'CUST_SAMSUNG',
                'legal_name' => 'Samsung Electronics Vietnam Co., Ltd.',
                'display_name' => 'Samsung Vietnam',
                'partner_kind' => 'ORGANIZATION',
                'tax_code' => '2300345678',
                'registration_no' => '2300345678',
                'website' => 'https://www.samsung.com',
                'default_currency_id' => $usdId,
                'payment_term_id' => $net30Id,
                'status' => 'ACTIVE',
                'notes' => 'Key account - Electronics & High-tech cargo',
                'roles' => ['CUSTOMER', 'SHIPPER'],
                'contact' => ['name' => 'Kim Jin Woo', 'title' => 'Logistics Manager', 'email' => 'jw.kim@samsung.com', 'phone' => '+84 24 3765 4321'],
                'address' => ['type' => 'OFFICE', 'line1' => 'Yen Phong Industrial Zone', 'ward' => 'Yen Trung', 'city' => 'Bac Ninh', 'country_id' => $vnCountryId],
                'credit_limit' => '100000.0000',
                'credit_currency' => $usdId,
                'credit_days' => 30,
            ],
            [
                'partner_code' => 'CUST_VINFAST',
                'legal_name' => 'VinFast Commercial and Services Trading LLC',
                'display_name' => 'VinFast Vietnam',
                'partner_kind' => 'ORGANIZATION',
                'tax_code' => '0108928392',
                'registration_no' => '0108928392',
                'website' => 'https://vinfastauto.com',
                'default_currency_id' => $vndId,
                'payment_term_id' => $net30Id,
                'status' => 'ACTIVE',
                'notes' => 'Electric Vehicles & Components Export',
                'roles' => ['CUSTOMER', 'SHIPPER'],
                'contact' => ['name' => 'Nguyen Van An', 'title' => 'Export Supervisor', 'email' => 'an.nv@vinfastauto.com', 'phone' => '+84 225 396 9999'],
                'address' => ['type' => 'OFFICE', 'line1' => 'Dinh Vu - Cat Hai Economic Zone', 'ward' => 'Dong Hai 2', 'city' => 'Hai Phong', 'country_id' => $vnCountryId],
                'credit_limit' => '2000000000.0000',
                'credit_currency' => $vndId,
                'credit_days' => 30,
            ],
            [
                'partner_code' => 'LINE_ONE',
                'legal_name' => 'Ocean Network Express (Vietnam) Co., Ltd.',
                'display_name' => 'ONE Line',
                'partner_kind' => 'ORGANIZATION',
                'tax_code' => '0314782910',
                'registration_no' => '0314782910',
                'website' => 'https://www.one-line.com',
                'default_currency_id' => $usdId,
                'payment_term_id' => null,
                'status' => 'ACTIVE',
                'notes' => 'Primary Ocean Carrier partner for Trans-Pacific & Intra-Asia',
                'roles' => ['CARRIER', 'VENDOR'],
                'contact' => ['name' => 'Tran Booking Desk', 'title' => 'Customer Service Lead', 'email' => 'booking@vn.one-line.com', 'phone' => '+84 28 3821 1234'],
                'address' => ['type' => 'OFFICE', 'line1' => 'MPlaza Saigon, 39 Le Duan', 'ward' => 'Ben Nghe', 'city' => 'Ho Chi Minh City', 'country_id' => $vnCountryId],
                'credit_limit' => null,
                'credit_currency' => $usdId,
                'credit_days' => 0,
            ],
            [
                'partner_code' => 'AGENT_KOREA',
                'legal_name' => 'Hankuk Global Logistics Korea Co., Ltd.',
                'display_name' => 'Hankuk Logistics Seoul',
                'partner_kind' => 'ORGANIZATION',
                'tax_code' => 'KR-102-81-99882',
                'registration_no' => '102-81-99882',
                'website' => 'https://hankuk-logistics.kr',
                'default_currency_id' => $usdId,
                'payment_term_id' => $net15Id,
                'status' => 'ACTIVE',
                'notes' => 'Overseas freight agent in South Korea - Incheon & Busan handling',
                'roles' => ['AGENT', 'COLOADER', 'VENDOR'],
                'contact' => ['name' => 'Park Min Soo', 'title' => 'Overseas Agency Manager', 'email' => 'mspark@hankuk-logistics.kr', 'phone' => '+82 2 555 1234'],
                'address' => ['type' => 'OFFICE', 'line1' => 'Teheran-ro, Gangnam-gu', 'ward' => 'Yeoksam-dong', 'city' => 'Seoul', 'country_id' => $krCountryId],
                'credit_limit' => '50000.0000',
                'credit_currency' => $usdId,
                'credit_days' => 15,
            ],
        ];

        foreach ($partners as $p) {
            DB::table('business_partners')->updateOrInsert(
                ['company_id' => $companyId, 'partner_code' => $p['partner_code']],
                [
                    'company_id' => $companyId,
                    'partner_code' => $p['partner_code'],
                    'legal_name' => $p['legal_name'],
                    'display_name' => $p['display_name'],
                    'partner_kind' => $p['partner_kind'],
                    'tax_code' => $p['tax_code'],
                    'registration_no' => $p['registration_no'],
                    'website' => $p['website'],
                    'default_currency_id' => $p['default_currency_id'],
                    'payment_term_id' => $p['payment_term_id'],
                    'status' => $p['status'],
                    'notes' => $p['notes'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $partnerId = DB::table('business_partners')
                ->where('company_id', $companyId)
                ->where('partner_code', $p['partner_code'])
                ->value('id');

            // Insert roles
            foreach ($p['roles'] as $roleCode) {
                DB::table('partner_roles')->updateOrInsert(
                    ['business_partner_id' => $partnerId, 'role_code' => $roleCode],
                    [
                        'company_id' => $companyId,
                        'business_partner_id' => $partnerId,
                        'role_code' => $roleCode,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            // Insert primary contact
            DB::table('partner_contacts')->updateOrInsert(
                ['company_id' => $companyId, 'business_partner_id' => $partnerId, 'email' => $p['contact']['email']],
                [
                    'company_id' => $companyId,
                    'business_partner_id' => $partnerId,
                    'full_name' => $p['contact']['name'],
                    'job_title' => $p['contact']['title'],
                    'email' => $p['contact']['email'],
                    'phone' => $p['contact']['phone'],
                    'is_primary' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Insert primary address
            DB::table('partner_addresses')->updateOrInsert(
                ['company_id' => $companyId, 'business_partner_id' => $partnerId, 'address_type' => $p['address']['type']],
                [
                    'company_id' => $companyId,
                    'business_partner_id' => $partnerId,
                    'address_type' => $p['address']['type'],
                    'contact_name' => $p['contact']['name'],
                    'phone' => $p['contact']['phone'],
                    'address_line1' => $p['address']['line1'],
                    'ward' => $p['address']['ward'],
                    'city' => $p['address']['city'],
                    'country_id' => $p['address']['country_id'],
                    'is_default' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Insert credit profile if specified
            if ($p['credit_limit'] !== null) {
                DB::table('partner_credit_profiles')->updateOrInsert(
                    ['company_id' => $companyId, 'business_partner_id' => $partnerId, 'effective_from' => '2026-01-01'],
                    [
                        'company_id' => $companyId,
                        'business_partner_id' => $partnerId,
                        'credit_limit' => $p['credit_limit'],
                        'currency_id' => $p['credit_currency'],
                        'credit_days' => $p['credit_days'],
                        'effective_from' => '2026-01-01',
                        'is_active' => true,
                        'approved_by' => $adminUserId,
                        'approved_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            // Assign sales rep if partner is customer
            if (in_array('CUSTOMER', $p['roles']) && $salesUserId) {
                DB::table('partner_sales_assignments')->updateOrInsert(
                    ['company_id' => $companyId, 'business_partner_id' => $partnerId, 'effective_from' => '2026-01-01'],
                    [
                        'company_id' => $companyId,
                        'business_partner_id' => $partnerId,
                        'sales_user_id' => $salesUserId,
                        'effective_from' => '2026-01-01',
                        'assignment_type' => 'PRIMARY',
                        'created_by' => $adminUserId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
