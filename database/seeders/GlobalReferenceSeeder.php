<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalReferenceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Currencies (ISO 4217)
        $currencies = [
            ['code' => 'VND', 'name' => 'Vietnamese Dong', 'minor_unit' => 0, 'symbol' => '₫', 'is_active' => true],
            ['code' => 'USD', 'name' => 'US Dollar', 'minor_unit' => 2, 'symbol' => '$', 'is_active' => true],
            ['code' => 'EUR', 'name' => 'Euro', 'minor_unit' => 2, 'symbol' => '€', 'is_active' => true],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'minor_unit' => 0, 'symbol' => '¥', 'is_active' => true],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'minor_unit' => 2, 'symbol' => '¥', 'is_active' => true],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'minor_unit' => 2, 'symbol' => 'S$', 'is_active' => true],
        ];
        foreach ($currencies as $c) {
            DB::table('currencies')->updateOrInsert(['code' => $c['code']], array_merge($c, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 2. Countries (ISO 3166-1)
        $countries = [
            ['iso2' => 'VN', 'iso3' => 'VNM', 'name' => 'Vietnam', 'phone_code' => '+84', 'is_active' => true],
            ['iso2' => 'US', 'iso3' => 'USA', 'name' => 'United States', 'phone_code' => '+1', 'is_active' => true],
            ['iso2' => 'CN', 'iso3' => 'CHN', 'name' => 'China', 'phone_code' => '+86', 'is_active' => true],
            ['iso2' => 'SG', 'iso3' => 'SGP', 'name' => 'Singapore', 'phone_code' => '+65', 'is_active' => true],
            ['iso2' => 'JP', 'iso3' => 'JPN', 'name' => 'Japan', 'phone_code' => '+81', 'is_active' => true],
            ['iso2' => 'KR', 'iso3' => 'KOR', 'name' => 'South Korea', 'phone_code' => '+82', 'is_active' => true],
            ['iso2' => 'TH', 'iso3' => 'THA', 'name' => 'Thailand', 'phone_code' => '+66', 'is_active' => true],
            ['iso2' => 'MY', 'iso3' => 'MYS', 'name' => 'Malaysia', 'phone_code' => '+60', 'is_active' => true],
            ['iso2' => 'DE', 'iso3' => 'DEU', 'name' => 'Germany', 'phone_code' => '+49', 'is_active' => true],
            ['iso2' => 'GB', 'iso3' => 'GBR', 'name' => 'United Kingdom', 'phone_code' => '+44', 'is_active' => true],
        ];
        foreach ($countries as $c) {
            DB::table('countries')->updateOrInsert(['iso2' => $c['iso2']], array_merge($c, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 3. Transport Modes
        $modes = [
            ['code' => 'OCEAN', 'name' => 'Ocean Freight', 'is_active' => true],
            ['code' => 'AIR', 'name' => 'Air Freight', 'is_active' => true],
            ['code' => 'ROAD', 'name' => 'Road Transport / Trucking', 'is_active' => true],
            ['code' => 'RAIL', 'name' => 'Rail Freight', 'is_active' => true],
            ['code' => 'MULTIMODAL', 'name' => 'Multimodal Transport', 'is_active' => true],
        ];
        foreach ($modes as $m) {
            DB::table('transport_modes')->updateOrInsert(['code' => $m['code']], array_merge($m, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 4. Package Types
        $packages = [
            ['code' => 'CTN', 'name' => 'Carton / Box', 'is_active' => true],
            ['code' => 'PLT', 'name' => 'Pallet', 'is_active' => true],
            ['code' => 'DRUM', 'name' => 'Drum', 'is_active' => true],
            ['code' => 'BAG', 'name' => 'Bag / Sack', 'is_active' => true],
            ['code' => 'CRATE', 'name' => 'Wooden Crate', 'is_active' => true],
            ['code' => 'ROLL', 'name' => 'Roll', 'is_active' => true],
            ['code' => 'PKG', 'name' => 'Package / Parcel', 'is_active' => true],
            ['code' => 'WOODEN_CASE', 'name' => 'Wooden Case', 'is_active' => true],
        ];
        foreach ($packages as $p) {
            DB::table('package_types')->updateOrInsert(['code' => $p['code']], array_merge($p, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 5. Container Types (ISO 6346)
        $containers = [
            ['code' => '20GP', 'name' => "20' General Purpose", 'iso_size_type_code' => '22G1', 'nominal_length_ft' => 20.00, 'is_reefer' => false, 'is_open_top' => false, 'is_active' => true],
            ['code' => '40GP', 'name' => "40' General Purpose", 'iso_size_type_code' => '42G1', 'nominal_length_ft' => 40.00, 'is_reefer' => false, 'is_open_top' => false, 'is_active' => true],
            ['code' => '40HC', 'name' => "40' High Cube", 'iso_size_type_code' => '45G1', 'nominal_length_ft' => 40.00, 'is_reefer' => false, 'is_open_top' => false, 'is_active' => true],
            ['code' => '45HC', 'name' => "45' High Cube", 'iso_size_type_code' => '45G1', 'nominal_length_ft' => 45.00, 'is_reefer' => false, 'is_open_top' => false, 'is_active' => true],
            ['code' => '20RF', 'name' => "20' Refrigerated", 'iso_size_type_code' => '22R1', 'nominal_length_ft' => 20.00, 'is_reefer' => true, 'is_open_top' => false, 'is_active' => true],
            ['code' => '40RF', 'name' => "40' Refrigerated", 'iso_size_type_code' => '45R1', 'nominal_length_ft' => 40.00, 'is_reefer' => true, 'is_open_top' => false, 'is_active' => true],
            ['code' => '20OT', 'name' => "20' Open Top", 'iso_size_type_code' => '22U1', 'nominal_length_ft' => 20.00, 'is_reefer' => false, 'is_open_top' => true, 'is_active' => true],
            ['code' => '40OT', 'name' => "40' Open Top", 'iso_size_type_code' => '42U1', 'nominal_length_ft' => 40.00, 'is_reefer' => false, 'is_open_top' => true, 'is_active' => true],
            ['code' => '20FR', 'name' => "20' Flat Rack", 'iso_size_type_code' => '22P1', 'nominal_length_ft' => 20.00, 'is_reefer' => false, 'is_open_top' => false, 'is_active' => true],
            ['code' => '40FR', 'name' => "40' Flat Rack", 'iso_size_type_code' => '42P1', 'nominal_length_ft' => 40.00, 'is_reefer' => false, 'is_open_top' => false, 'is_active' => true],
        ];
        foreach ($containers as $c) {
            DB::table('container_types')->updateOrInsert(['code' => $c['code']], array_merge($c, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 6. Charge Categories
        $categories = [
            ['code' => 'FREIGHT', 'name' => 'Basic Transport Freight', 'financial_side' => 'BOTH', 'is_active' => true],
            ['code' => 'ORIGIN_LOCAL', 'name' => 'Origin Local Charges', 'financial_side' => 'BOTH', 'is_active' => true],
            ['code' => 'DEST_LOCAL', 'name' => 'Destination Local Charges', 'financial_side' => 'BOTH', 'is_active' => true],
            ['code' => 'CUSTOMS', 'name' => 'Customs Clearance & Brokerage', 'financial_side' => 'BOTH', 'is_active' => true],
            ['code' => 'INLAND', 'name' => 'Inland Transport & Haulage', 'financial_side' => 'BOTH', 'is_active' => true],
            ['code' => 'WAREHOUSE', 'name' => 'CFS & Warehousing Services', 'financial_side' => 'BOTH', 'is_active' => true],
            ['code' => 'DOCUMENTATION', 'name' => 'Documentation & Admin Fees', 'financial_side' => 'BOTH', 'is_active' => true],
            ['code' => 'OTHER', 'name' => 'Miscellaneous Services', 'financial_side' => 'BOTH', 'is_active' => true],
        ];
        foreach ($categories as $cat) {
            DB::table('charge_categories')->updateOrInsert(['code' => $cat['code']], array_merge($cat, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 7. Service Types
        $oceanModeId = DB::table('transport_modes')->where('code', 'OCEAN')->value('id');
        $airModeId = DB::table('transport_modes')->where('code', 'AIR')->value('id');
        $roadModeId = DB::table('transport_modes')->where('code', 'ROAD')->value('id');

        $services = [
            ['code' => 'OCEAN_FCL_EXP', 'name' => 'Ocean FCL Export', 'transport_mode_id' => $oceanModeId, 'sort_order' => 10, 'is_active' => true],
            ['code' => 'OCEAN_FCL_IMP', 'name' => 'Ocean FCL Import', 'transport_mode_id' => $oceanModeId, 'sort_order' => 20, 'is_active' => true],
            ['code' => 'OCEAN_LCL_EXP', 'name' => 'Ocean LCL Export', 'transport_mode_id' => $oceanModeId, 'sort_order' => 30, 'is_active' => true],
            ['code' => 'OCEAN_LCL_IMP', 'name' => 'Ocean LCL Import', 'transport_mode_id' => $oceanModeId, 'sort_order' => 40, 'is_active' => true],
            ['code' => 'AIR_EXP', 'name' => 'Air Freight Export', 'transport_mode_id' => $airModeId, 'sort_order' => 50, 'is_active' => true],
            ['code' => 'AIR_IMP', 'name' => 'Air Freight Import', 'transport_mode_id' => $airModeId, 'sort_order' => 60, 'is_active' => true],
            ['code' => 'TRUCKING_DOM', 'name' => 'Inland Trucking & Transport', 'transport_mode_id' => $roadModeId, 'sort_order' => 70, 'is_active' => true],
            ['code' => 'CUSTOMS_SVC', 'name' => 'Customs Clearance Brokerage', 'transport_mode_id' => null, 'sort_order' => 80, 'is_active' => true],
        ];
        foreach ($services as $s) {
            DB::table('service_types')->updateOrInsert(['code' => $s['code']], array_merge($s, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
