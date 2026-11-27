<?php

use Illuminate\Support\Facades\Route;

// Cargo FMS Dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('pages.dashboard.fms', ['title' => 'Freight Operations Overview']);
})->name('dashboard');

Route::get('/ecommerce', function () {
    return view('pages.dashboard.ecommerce', ['title' => 'E-commerce Dashboard']);
})->name('ecommerce');

// Cargo FMS Domain Routes helper
$fmsModule = function ($title, $description, array $tables = []) {
    return fn () => view('pages.common.placeholder', [
        'title' => $title,
        'description' => $description,
        'tables' => $tables,
    ]);
};

// 1. Operations
Route::get('/analytics', $fmsModule('Operations & Freight Analytics', 'Volume, route density, TEU throughput, carrier performance, and margin analytics.', ['shipments', 'service_types', 'ports', 'airports']))->name('fms.analytics');

// Sales & CRM
Route::prefix('sales')->name('sales.')->group(function () use ($fmsModule) {
    Route::get('/inquiries', $fmsModule('Customer Inquiries', 'Capture and track shipper freight inquiries across Ocean, Air, and Haulage modes.', ['business_partners', 'numbering_sequences', 'locations']))->name('inquiries');
    Route::get('/quotations', fn () => view('pages.sales.quotations.index', ['title' => 'Freight Quotations']))->name('quotations');
    Route::get('/orders', $fmsModule('Sales Orders', 'Confirmed freight bookings and sales orders ready for operations turnover.', ['business_partners', 'service_types', 'payment_terms']))->name('orders');
});

// Freight Operations
Route::prefix('operations')->name('operations.')->group(function () use ($fmsModule) {
    Route::get('/shipments', $fmsModule('Freight Shipments (Jobs)', 'Central operational workbench managing active Ocean, Air, and Land freight jobs.', ['service_types', 'transport_modes', 'ports', 'airports', 'numbering_sequences']))->name('shipments');
    Route::get('/bookings', $fmsModule('Carrier Bookings', 'Carrier booking requests, confirmations, allocations, and empty equipment pick-up.', ['business_partners', 'container_types', 'numbering_sequences']))->name('bookings');
    Route::get('/ocean-export', $fmsModule('Ocean Export FCL', 'Full Container Load export management, container stuffing, and shipping instructions.', ['ports', 'container_types', 'service_types']))->name('ocean-export');
    Route::get('/ocean-import', $fmsModule('Ocean Import FCL', 'Inbound Ocean shipments, vessel tracking, container demurrage/detention, and release.', ['ports', 'container_types', 'locations']))->name('ocean-import');
    Route::get('/air-freight', $fmsModule('Air Freight Operations', 'Air cargo consignments, gross/chargeable weight calculations, and airport handling.', ['airports', 'package_types', 'service_types']))->name('air-freight');
});

// Documentation
Route::prefix('docs')->name('docs.')->group(function () use ($fmsModule) {
    Route::get('/hbl', $fmsModule('House Bill of Lading (HBL / HAWB)', 'Issue, print, and amend House B/L and Air Waybills for cargo release.', ['numbering_sequences', 'business_partners', 'ports']))->name('hbl');
    Route::get('/mbl', $fmsModule('Master Bill of Lading (MBL / MAWB)', 'Manage carrier Master Bills of Lading, shipping line consignments, and freight manifest.', ['business_partners', 'ports', 'airports']))->name('mbl');
    Route::get('/arrival-notices', $fmsModule('Arrival Notices', 'Dispatch automated Arrival Notices to notify consignees of incoming freight.', ['business_partners', 'ports', 'numbering_sequences']))->name('arrival-notices');
    Route::get('/delivery-orders', $fmsModule('Delivery Orders (D/O)', 'Issue electronic and paper D/O for container and cargo release at sea/air terminals.', ['business_partners', 'ports', 'container_types']))->name('delivery-orders');
});

// Logistics & Haulage
Route::prefix('logistics')->name('logistics.')->group(function () use ($fmsModule) {
    Route::get('/transport-orders', $fmsModule('Transport Orders', 'Issue haulage orders to domestic trucking vendors and inland depots.', ['numbering_sequences', 'business_partners', 'locations']))->name('transport-orders');
    Route::get('/dispatch', $fmsModule('Dispatch & Trucking', 'Container drayage dispatch, driver coordination, and gate-in/gate-out tracking.', ['business_partners', 'container_types']))->name('dispatch');
    Route::get('/pod', $fmsModule('Proof of Delivery (POD)', 'Capture signed delivery receipts, e-POD images, and recipient confirmation.', ['business_partners', 'locations']))->name('pod');
});

// 2. Finance & Billing
Route::prefix('billing')->name('billing.')->group(function () use ($fmsModule) {
    Route::get('/charges', $fmsModule('Shipment Charges & Costing', 'Manage buy/sell charge lines, currency conversions, and gross profit reconciliation.', ['charge_codes', 'charge_categories', 'currencies', 'exchange_rates']))->name('charges');
    Route::get('/debit-notes', $fmsModule('Debit Notes', 'Issue debit notes to overseas freight agents and clients for origin/destination charges.', ['numbering_sequences', 'business_partners', 'currencies']))->name('debit-notes');
    Route::get('/credit-notes', $fmsModule('Credit Notes', 'Process billing adjustments, rebates, and credit notes.', ['numbering_sequences', 'business_partners', 'currencies']))->name('credit-notes');
});

Route::prefix('accounting')->name('accounting.')->group(function () use ($fmsModule) {
    Route::get('/invoices', $fmsModule('Customer Invoices', 'Commercial VAT invoices, e-invoicing integration, and AR receivables ledger.', ['tax_codes', 'payment_terms', 'currencies', 'numbering_sequences']))->name('invoices');
    Route::get('/payables', $fmsModule('Carrier & Vendor Payables', 'AP freight bills, carrier cost matching, and vendor disbursement control.', ['business_partners', 'payment_terms', 'currencies']))->name('payables');
    Route::get('/payments', $fmsModule('Receipts & Payments', 'Bank settlements, customer collections, and multi-currency exchange gain/loss.', ['currencies', 'exchange_rates', 'payment_terms']))->name('payments');
});

// 3. Master Data & System
Route::prefix('master')->name('master.')->group(function () use ($fmsModule) {
    Route::get('/partners', function () {
        return view('pages.master.partners.index', ['title' => 'Business Partners Directory']);
    })->name('partners');
    Route::get('/credit-profiles', $fmsModule('Partner Credit Profiles', 'Credit limits, payment terms, currency restrictions, and risk status.', ['partner_credit_profiles', 'payment_terms', 'currencies']))->name('credit-profiles');
    Route::get('/sales-assignments', $fmsModule('Partner Sales Assignments', 'Assign account executives and commission splits per business partner.', ['partner_sales_assignments', 'users']))->name('sales-assignments');
    Route::get('/ports-airports', $fmsModule('Ports & Airports Directory', 'UN/LOCODE seaports, IATA/ICAO airports, country codes, and customs locations.', ['ports', 'airports', 'countries', 'locations']))->name('ports-airports');
    Route::get('/charge-codes', $fmsModule('Charge Codes & Tariffs', 'Master catalog of freight charges (O/F, THC, D/O, CFS, CIC) with default accounting codes.', ['charge_codes', 'charge_categories', 'currencies']))->name('charge-codes');
    Route::get('/exchange-rates', $fmsModule('Exchange Rates & FX', 'Daily commercial and operational FX rates against VND base currency.', ['exchange_rates', 'currencies']))->name('exchange-rates');
});

Route::prefix('system')->name('system.')->group(function () use ($fmsModule) {
    Route::get('/branches', $fmsModule('Company & Branches', 'Multi-company, multi-branch organizational hierarchy (CARGO_VN, SGN, HAN, DAD).', ['companies', 'branches', 'departments']))->name('branches');
    Route::get('/sequences', $fmsModule('Document Numbering Sequences', 'Automated document numbering sequences with branch scope and year reset (SH26-, HBL26-, INV26-).', ['numbering_sequences', 'branches']))->name('sequences');
    Route::get('/users', $fmsModule('Users & Multi-Branch Access', 'User credentials, branch permissions, and role-based access control (RBAC).', ['users', 'user_company_access', 'user_branch_access']))->name('users');
});

// Standard TailAdmin Pages (Preserved)
Route::get('/calendar', function () {
    return view('pages.calendar', ['title' => 'Calendar']);
})->name('calendar');

Route::get('/profile', function () {
    return view('pages.profile', ['title' => 'Profile']);
})->name('profile');

Route::get('/form-elements', function () {
    return view('pages.forms.form-elements', ['title' => 'Form Elements']);
})->name('form-elements');

Route::get('/basic-tables', function () {
    return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
})->name('basic-tables');

Route::get('/blank', function () {
    return view('pages.pages.blank', ['title' => 'Blank']);
})->name('blank');

Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');

Route::get('/line-chart', function () {
    return view('pages.chart.line-chart', ['title' => 'Line Chart']);
})->name('line-chart');

Route::get('/bar-chart', function () {
    return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
})->name('bar-chart');

Route::get('/signin', function () {
    return view('pages.auth.signin', ['title' => 'Sign In']);
})->name('signin');

Route::get('/signup', function () {
    return view('pages.auth.signup', ['title' => 'Sign Up']);
})->name('signup');

Route::post('/logout', function () {
    return redirect()->route('signin');
})->name('logout');

Route::get('/alerts', function () {
    return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
})->name('alerts');

Route::get('/avatars', function () {
    return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
})->name('avatars');

Route::get('/badge', function () {
    return view('pages.ui-elements.badge', ['title' => 'Badges']);
})->name('badge');

Route::get('/buttons', function () {
    return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
})->name('buttons');

Route::get('/images', function () {
    return view('pages.ui-elements.images', ['title' => 'Images']);
})->name('images');

Route::get('/videos', function () {
    return view('pages.ui-elements.videos', ['title' => 'Videos']);
})->name('videos');

Route::redirect('/badges', '/badge');
