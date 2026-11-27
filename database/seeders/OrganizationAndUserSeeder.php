<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class OrganizationAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $vndId = DB::table('currencies')->where('code', 'VND')->value('id');

        // 1. Company
        $companyId = DB::table('companies')->updateOrInsert(
            ['code' => 'CARGO_VN'],
            [
                'legal_name' => 'Cargo Logistics Vietnam Joint Stock Company',
                'display_name' => 'Cargo FMS Vietnam',
                'tax_code' => '0102030405',
                'base_currency_id' => $vndId,
                'timezone' => 'Asia/Ho_Chi_Minh',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $companyId = DB::table('companies')->where('code', 'CARGO_VN')->value('id');

        // Set team context for Spatie permissions
        setPermissionsTeamId($companyId);

        // 2. Branches
        $branches = [
            ['code' => 'SGN_HQ', 'name' => 'Ho Chi Minh Head Office', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_head_office' => true, 'is_active' => true],
            ['code' => 'HPH_BR', 'name' => 'Hai Phong Branch Office', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_head_office' => false, 'is_active' => true],
            ['code' => 'HAN_BR', 'name' => 'Ha Noi Representative Office', 'timezone' => 'Asia/Ho_Chi_Minh', 'is_head_office' => false, 'is_active' => true],
        ];

        foreach ($branches as $br) {
            DB::table('branches')->updateOrInsert(
                ['company_id' => $companyId, 'code' => $br['code']],
                array_merge($br, [
                    'company_id' => $companyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $sgnBranchId = DB::table('branches')->where('company_id', $companyId)->where('code', 'SGN_HQ')->value('id');
        $hphBranchId = DB::table('branches')->where('company_id', $companyId)->where('code', 'HPH_BR')->value('id');

        // 3. Departments
        $departments = [
            ['code' => 'BOD', 'name' => 'Board of Directors', 'branch_id' => null, 'is_active' => true],
            ['code' => 'SALES', 'name' => 'Commercial & Sales Department', 'branch_id' => null, 'is_active' => true],
            ['code' => 'OPS', 'name' => 'Freight Operations Department', 'branch_id' => null, 'is_active' => true],
            ['code' => 'DOCS', 'name' => 'Documentation Department', 'branch_id' => null, 'is_active' => true],
            ['code' => 'ACC', 'name' => 'Finance & Accounting Department', 'branch_id' => null, 'is_active' => true],
            ['code' => 'LOG', 'name' => 'Inland Transport & Logistics', 'branch_id' => null, 'is_active' => true],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->updateOrInsert(
                ['company_id' => $companyId, 'code' => $dept['code']],
                array_merge($dept, [
                    'company_id' => $companyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // 4. Spatie Roles (Teams mode: company_id)
        $roleNames = [
            'Super Admin',
            'Operations Manager',
            'Sales Executive',
            'Documentation Specialist',
            'Accountant',
        ];

        $roles = [];
        foreach ($roleNames as $roleName) {
            $roles[$roleName] = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
                'company_id' => $companyId,
            ]);
        }

        // 5. Users
        $defaultPassword = Hash::make('password');

        $usersData = [
            [
                'name' => 'Administrator',
                'email' => 'admin@cargofms.com',
                'role' => 'Super Admin',
                'access_level' => 'ADMIN',
                'branches' => [$sgnBranchId, $hphBranchId],
            ],
            [
                'name' => 'Nguyen Van Sales',
                'email' => 'sales@cargofms.com',
                'role' => 'Sales Executive',
                'access_level' => 'MEMBER',
                'branches' => [$sgnBranchId],
            ],
            [
                'name' => 'Tran Thi Ops',
                'email' => 'ops@cargofms.com',
                'role' => 'Operations Manager',
                'access_level' => 'MEMBER',
                'branches' => [$sgnBranchId, $hphBranchId],
            ],
            [
                'name' => 'Le Van Docs',
                'email' => 'docs@cargofms.com',
                'role' => 'Documentation Specialist',
                'access_level' => 'MEMBER',
                'branches' => [$sgnBranchId],
            ],
            [
                'name' => 'Pham Thi Accountant',
                'email' => 'acc@cargofms.com',
                'role' => 'Accountant',
                'access_level' => 'MEMBER',
                'branches' => [$sgnBranchId],
            ],
        ];

        foreach ($usersData as $ud) {
            $user = User::updateOrCreate(
                ['email' => $ud['email']],
                [
                    'name' => $ud['name'],
                    'password' => $defaultPassword,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            // Assign role within company context
            $user->assignRole($roles[$ud['role']]);

            // Assign user company access
            DB::table('user_company_access')->updateOrInsert(
                ['user_id' => $user->id, 'company_id' => $companyId],
                [
                    'access_level' => $ud['access_level'],
                    'is_default' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Assign user branch access
            foreach ($ud['branches'] as $branchId) {
                if ($branchId) {
                    DB::table('user_branch_access')->updateOrInsert(
                        ['user_id' => $user->id, 'branch_id' => $branchId],
                        [
                            'company_id' => $companyId,
                            'is_active' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }
    }
}
