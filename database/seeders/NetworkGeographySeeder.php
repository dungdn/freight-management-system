<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NetworkGeographySeeder extends Seeder
{
    public function run(): void
    {
        $vnId = DB::table('countries')->where('iso2', 'VN')->value('id');
        $usId = DB::table('countries')->where('iso2', 'US')->value('id');
        $sgId = DB::table('countries')->where('iso2', 'SG')->value('id');
        $cnId = DB::table('countries')->where('iso2', 'CN')->value('id');

        // 1. Locations
        $locations = [
            ['country_id' => $vnId, 'code' => 'SGN', 'name' => 'Ho Chi Minh City', 'location_type' => 'CITY', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true],
            ['country_id' => $vnId, 'code' => 'HPH', 'name' => 'Hai Phong', 'location_type' => 'CITY', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true],
            ['country_id' => $vnId, 'code' => 'HAN', 'name' => 'Ha Noi', 'location_type' => 'CITY', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true],
            ['country_id' => $vnId, 'code' => 'DAD', 'name' => 'Da Nang', 'location_type' => 'CITY', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true],
            ['country_id' => $sgId, 'code' => 'SIN', 'name' => 'Singapore', 'location_type' => 'CITY', 'timezone' => 'Asia/Singapore', 'is_active' => true],
            ['country_id' => $usId, 'code' => 'LAX', 'name' => 'Los Angeles', 'location_type' => 'CITY', 'timezone' => 'America/Los_Angeles', 'is_active' => true],
            ['country_id' => $cnId, 'code' => 'SHA', 'name' => 'Shanghai', 'location_type' => 'CITY', 'timezone' => 'Asia/Shanghai', 'is_active' => true],
        ];

        foreach ($locations as $loc) {
            DB::table('locations')->updateOrInsert(
                ['country_id' => $loc['country_id'], 'location_type' => $loc['location_type'], 'code' => $loc['code']],
                array_merge($loc, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $sgnLocId = DB::table('locations')->where('code', 'SGN')->value('id');
        $hphLocId = DB::table('locations')->where('code', 'HPH')->value('id');
        $hanLocId = DB::table('locations')->where('code', 'HAN')->value('id');
        $dadLocId = DB::table('locations')->where('code', 'DAD')->value('id');
        $sinLocId = DB::table('locations')->where('code', 'SIN')->value('id');
        $laxLocId = DB::table('locations')->where('code', 'LAX')->value('id');
        $shaLocId = DB::table('locations')->where('code', 'SHA')->value('id');

        // 2. Ports (UN/LOCODE)
        $ports = [
            ['unlocode' => 'VNSGN', 'name' => 'Cat Lai / Ho Chi Minh Port', 'country_id' => $vnId, 'location_id' => $sgnLocId, 'port_type' => 'SEAPORT', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true],
            ['unlocode' => 'VNHPH', 'name' => 'Hai Phong Port', 'country_id' => $vnId, 'location_id' => $hphLocId, 'port_type' => 'SEAPORT', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true],
            ['unlocode' => 'VNDAD', 'name' => 'Da Nang Port', 'country_id' => $vnId, 'location_id' => $dadLocId, 'port_type' => 'SEAPORT', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true],
            ['unlocode' => 'VNVUT', 'name' => 'Cai Mep / Thi Vai Port', 'country_id' => $vnId, 'location_id' => $sgnLocId, 'port_type' => 'SEAPORT', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true],
            ['unlocode' => 'SGSIN', 'name' => 'Port of Singapore', 'country_id' => $sgId, 'location_id' => $sinLocId, 'port_type' => 'SEAPORT', 'timezone' => 'Asia/Singapore', 'is_active' => true],
            ['unlocode' => 'USLAX', 'name' => 'Port of Los Angeles', 'country_id' => $usId, 'location_id' => $laxLocId, 'port_type' => 'SEAPORT', 'timezone' => 'America/Los_Angeles', 'is_active' => true],
            ['unlocode' => 'USLGB', 'name' => 'Port of Long Beach', 'country_id' => $usId, 'location_id' => $laxLocId, 'port_type' => 'SEAPORT', 'timezone' => 'America/Los_Angeles', 'is_active' => true],
            ['unlocode' => 'CNSHA', 'name' => 'Port of Shanghai', 'country_id' => $cnId, 'location_id' => $shaLocId, 'port_type' => 'SEAPORT', 'timezone' => 'Asia/Shanghai', 'is_active' => true],
            ['unlocode' => 'CNNBO', 'name' => 'Port of Ningbo-Zhoushan', 'country_id' => $cnId, 'location_id' => null, 'port_type' => 'SEAPORT', 'timezone' => 'Asia/Shanghai', 'is_active' => true],
        ];

        foreach ($ports as $port) {
            DB::table('ports')->updateOrInsert(
                ['unlocode' => $port['unlocode']],
                array_merge($port, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 3. Airports (IATA / ICAO)
        $airports = [
            ['iata_code' => 'SGN', 'icao_code' => 'VVTS', 'name' => 'Tan Son Nhat International Airport', 'country_id' => $vnId, 'location_id' => $sgnLocId, 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true],
            ['iata_code' => 'HAN', 'icao_code' => 'VVNB', 'name' => 'Noi Bai International Airport', 'country_id' => $vnId, 'location_id' => $hanLocId, 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true],
            ['iata_code' => 'DAD', 'icao_code' => 'VVDN', 'name' => 'Da Nang International Airport', 'country_id' => $vnId, 'location_id' => $dadLocId, 'timezone' => 'Asia/Ho_Chi_Minh', 'is_active' => true],
            ['iata_code' => 'SIN', 'icao_code' => 'WSSS', 'name' => 'Singapore Changi Airport', 'country_id' => $sgId, 'location_id' => $sinLocId, 'timezone' => 'Asia/Singapore', 'is_active' => true],
            ['iata_code' => 'LAX', 'icao_code' => 'KLAX', 'name' => 'Los Angeles International Airport', 'country_id' => $usId, 'location_id' => $laxLocId, 'timezone' => 'America/Los_Angeles', 'is_active' => true],
            ['iata_code' => 'PVG', 'icao_code' => 'ZSPD', 'name' => 'Shanghai Pudong International Airport', 'country_id' => $cnId, 'location_id' => $shaLocId, 'timezone' => 'Asia/Shanghai', 'is_active' => true],
        ];

        foreach ($airports as $airport) {
            DB::table('airports')->updateOrInsert(
                ['iata_code' => $airport['iata_code']],
                array_merge($airport, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
