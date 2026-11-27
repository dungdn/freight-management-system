<?php

test('homepage redirects to cargo fms dashboard', function () {
    $response = $this->get('/');

    $response->assertRedirect('/dashboard');
});

test('cargo fms dashboard returns a successful response', function () {
    $response = $this->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Freight Operations Overview');
    $response->assertSee('Active Shipments (Jobs)');
    $response->assertSee('Freight Milestone Pipeline');
    $response->assertSee('Recent Shipments (Jobs)');
});

test('cargo fms operations modules return a successful response', function () {
    $response = $this->get('/operations/shipments');

    $response->assertStatus(200);
    $response->assertSee('Freight Shipments (Jobs)');
});

test('cargo fms master data modules return a successful response', function () {
    $response = $this->get('/master/partners');

    $response->assertStatus(200);
    $response->assertSee('Business Partners Directory');
});

test('cargo fms billing modules return a successful response', function () {
    $response = $this->get('/billing/charges');

    $response->assertStatus(200);
    $response->assertSee('Shipment Charges & Costing');
});
