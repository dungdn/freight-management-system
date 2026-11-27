<?php

use App\Livewire\Master\PartnerManager;
use App\Models\BusinessPartner;
use App\Models\Company;
use App\Support\Tenancy\TenantContext;
use Livewire\Livewire;

beforeEach(function () {
    $company = Company::where('code', 'CARGO_VN')->first();
    TenantContext::setCompanyId($company->id);
});

test('partner management page renders successfully with livewire component', function () {
    $response = $this->get('/master/partners');

    $response->assertStatus(200);
    $response->assertSeeLivewire(PartnerManager::class);
    $response->assertSee('Business Partners Directory');
    $response->assertSee('CUST_SAMSUNG');
    $response->assertSee('LINE_ONE');
});

test('partner manager component filters by search query and role', function () {
    Livewire::test(PartnerManager::class)
        ->assertSee('CUST_SAMSUNG')
        ->assertSee('CUST_VINFAST')
        ->assertSee('LINE_ONE')
        // Test search
        ->set('search', 'Samsung')
        ->assertSee('CUST_SAMSUNG')
        ->assertDontSee('CUST_VINFAST')
        // Test role filter
        ->set('search', '')
        ->set('selectedRole', 'CARRIER')
        ->assertSee('LINE_ONE')
        ->assertDontSee('CUST_SAMSUNG');
});

test('partner manager creates a new business partner with contact, address and credit profile', function () {
    Livewire::test(PartnerManager::class)
        ->call('openCreate')
        ->set('partner_code', 'TEST_NEW_PARTNER')
        ->set('legal_name', 'Test Freight Logistics Partner Co.')
        ->set('display_name', 'Test Freight')
        ->set('partner_kind', 'ORGANIZATION')
        ->set('tax_code', '0109998888')
        ->set('roles', ['CUSTOMER', 'SHIPPER', 'CONSIGNEE'])
        ->set('contact_name', 'David Tran')
        ->set('contact_email', 'david@testfreight.com')
        ->set('contact_phone', '+84 90 999 8888')
        ->set('address_line1', '123 Nguyen Hue Boulevard')
        ->set('city', 'Ho Chi Minh City')
        ->set('credit_limit', '50000')
        ->set('credit_days', 45)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Test Freight Logistics Partner Co.');

    // Verify in database
    $partner = BusinessPartner::where('partner_code', 'TEST_NEW_PARTNER')->first();
    expect($partner)->not->toBeNull();
    expect($partner->legal_name)->toBe('Test Freight Logistics Partner Co.');
    expect($partner->hasRole('SHIPPER'))->toBeTrue();
    expect($partner->hasRole('CONSIGNEE'))->toBeTrue();
    expect($partner->primaryContact)->not->toBeNull();
    expect($partner->primaryContact->email)->toBe('david@testfreight.com');
    expect($partner->primaryAddress)->not->toBeNull();
    expect($partner->primaryAddress->city)->toBe('Ho Chi Minh City');
    expect($partner->activeCreditProfile)->not->toBeNull();
    expect((int)$partner->activeCreditProfile->credit_days)->toBe(45);
});

test('partner manager can edit and update an existing partner', function () {
    $partner = BusinessPartner::where('partner_code', 'TEST_NEW_PARTNER')->first();
    expect($partner)->not->toBeNull();

    Livewire::test(PartnerManager::class)
        ->call('openEdit', $partner->id)
        ->assertSet('partner_code', 'TEST_NEW_PARTNER')
        ->set('legal_name', 'Updated Test Logistics Partner Co.')
        ->call('save')
        ->assertHasNoErrors();

    $partner->refresh();
    expect($partner->legal_name)->toBe('Updated Test Logistics Partner Co.');
});

test('partner manager can toggle partner status and delete partner', function () {
    $partner = BusinessPartner::where('partner_code', 'TEST_NEW_PARTNER')->first();
    expect($partner)->not->toBeNull();

    // Toggle status
    Livewire::test(PartnerManager::class)
        ->call('toggleStatus', $partner->id);

    $partner->refresh();
    expect($partner->status)->toBe('HOLD');

    // Delete
    Livewire::test(PartnerManager::class)
        ->call('deletePartner', $partner->id);

    expect(BusinessPartner::where('partner_code', 'TEST_NEW_PARTNER')->exists())->toBeFalse();
});
