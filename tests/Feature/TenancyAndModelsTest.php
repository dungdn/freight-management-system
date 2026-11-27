<?php

use App\Models\Branch;
use App\Models\BusinessPartner;
use App\Models\ChargeCode;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Department;
use App\Models\NumberingSequence;
use App\Models\Port;
use App\Models\User;
use App\Services\Sequence\NumberingSequenceService;
use App\Support\Tenancy\TenantContext;

beforeEach(function () {
    TenantContext::clearContext();
});

test('tenant context stores and manages active company and branch', function () {
    expect(TenantContext::getCompanyId())->toBeNull();
    expect(TenantContext::getBranchId())->toBeNull();

    TenantContext::setContext(1, 2);

    expect(TenantContext::getCompanyId())->toBe(1);
    expect(TenantContext::getBranchId())->toBe(2);

    TenantContext::withoutTenancy(function () {
        expect(TenantContext::isTenancyEnabled())->toBeFalse();
        expect(TenantContext::getCompanyId())->toBeNull();
    });

    expect(TenantContext::isTenancyEnabled())->toBeTrue();
    expect(TenantContext::getCompanyId())->toBe(1);
});

test('company scope isolates data between companies', function () {
    $companyA = Company::where('code', 'CARGO_VN')->first();
    expect($companyA)->not->toBeNull();

    // Create a temporary second company to test isolation
    $currency = Currency::where('code', 'VND')->first();
    $companyB = Company::create([
        'code' => 'TEST_GLOBAL',
        'legal_name' => 'Test Global Logistics Co.',
        'base_currency_id' => $currency->id,
        'timezone' => 'Asia/Singapore',
        'is_active' => true,
    ]);

    // Create a partner under company B
    $partnerB = BusinessPartner::create([
        'company_id' => $companyB->id,
        'partner_code' => 'CUST_TEST_B',
        'legal_name' => 'Company B Exclusive Customer',
        'partner_kind' => 'ORGANIZATION',
        'status' => 'ACTIVE',
    ]);

    // Set active tenant to Company A
    TenantContext::setCompanyId($companyA->id);

    // Company A should see seeded partners (Samsung, VinFast, ONE Line) but NOT Company B's partner
    $partnersForA = BusinessPartner::all();
    expect($partnersForA->pluck('partner_code')->contains('CUST_TEST_B'))->toBeFalse();
    expect($partnersForA->pluck('partner_code')->contains('CUST_SAMSUNG'))->toBeTrue();

    // Switch active tenant to Company B
    TenantContext::setCompanyId($companyB->id);
    $partnersForB = BusinessPartner::all();
    expect($partnersForB->pluck('partner_code')->contains('CUST_TEST_B'))->toBeTrue();
    expect($partnersForB->pluck('partner_code')->contains('CUST_SAMSUNG'))->toBeFalse();

    // Clean up test company B
    TenantContext::withoutTenancy(function () use ($partnerB, $companyB) {
        $partnerB->delete();
        $companyB->delete();
    });
});

test('belongs to company trait automatically injects company id on creation', function () {
    $company = Company::where('code', 'CARGO_VN')->first();
    TenantContext::setCompanyId($company->id);

    // Create partner without passing company_id
    $partner = BusinessPartner::create([
        'partner_code' => 'AUTO_TENANT_01',
        'legal_name' => 'Auto Tenant Shipper Ltd',
        'partner_kind' => 'ORGANIZATION',
        'status' => 'ACTIVE',
    ]);

    expect($partner->company_id)->toBe($company->id);

    // Clean up
    $partner->delete();
});

test('business partner relationships and role helpers work correctly', function () {
    $company = Company::where('code', 'CARGO_VN')->first();
    TenantContext::setCompanyId($company->id);

    $samsung = BusinessPartner::where('partner_code', 'CUST_SAMSUNG')->first();
    expect($samsung)->not->toBeNull();

    // Check roles
    expect($samsung->roles)->not->toBeEmpty();
    expect($samsung->hasRole('CUSTOMER'))->toBeTrue();
    expect($samsung->hasRole('SHIPPER'))->toBeTrue();
    expect($samsung->hasRole('CARRIER'))->toBeFalse();

    // Check contacts and addresses
    expect($samsung->contacts)->not->toBeEmpty();
    expect($samsung->primaryContact)->not->toBeNull();
    expect($samsung->addresses)->not->toBeEmpty();

    // Check line ONE carrier
    $oneLine = BusinessPartner::where('partner_code', 'LINE_ONE')->first();
    expect($oneLine)->not->toBeNull();
    expect($oneLine->hasRole('CARRIER'))->toBeTrue();
});

test('organization models and branch relationships work correctly', function () {
    $company = Company::where('code', 'CARGO_VN')->first();
    expect($company->branches)->not->toBeEmpty();

    $sgnBranch = $company->branches()->where('code', 'SGN_HQ')->first();
    expect($sgnBranch)->not->toBeNull();
    expect($sgnBranch->is_head_office)->toBeTrue();

    // Check departments
    expect($company->departments)->not->toBeEmpty();
    expect($company->departments()->pluck('code')->contains('OPS'))->toBeTrue();
});

test('numbering sequence service allocates sequential unique numbers with concurrency safety', function () {
    $company = Company::where('code', 'CARGO_VN')->first();
    TenantContext::setCompanyId($company->id);

    $service = app(NumberingSequenceService::class);

    // Peek should not increment
    $peek1 = $service->peekNext('SHIPMENT');
    $peek2 = $service->peekNext('SHIPMENT');
    expect($peek1)->toBe($peek2);
    expect($peek1)->toStartWith('SH26-');

    // Generate sequentially
    $num1 = $service->generate('SHIPMENT');
    $num2 = $service->generate('SHIPMENT');

    expect($num1)->toStartWith('SH26-');
    expect($num2)->toStartWith('SH26-');
    expect($num1)->not->toBe($num2);

    // Difference between sequential numbers
    $seq1 = (int) str_replace('SH26-', '', $num1);
    $seq2 = (int) str_replace('SH26-', '', $num2);
    expect($seq2)->toBe($seq1 + 1);

    // Test other document types
    $quotationNo = $service->generate('QUOTATION');
    expect($quotationNo)->toStartWith('QT26-');

    $bookingNo = $service->generate('BOOKING');
    expect($bookingNo)->toStartWith('BK26-');

    $hblNo = $service->generate('HBL');
    expect($hblNo)->toStartWith('HBL26-');
});
