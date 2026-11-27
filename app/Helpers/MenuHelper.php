<?php

namespace App\Helpers;

class MenuHelper
{
    public static function getOperationsItems()
    {
        return [
            [
                'icon' => 'dashboard',
                'name' => 'Dashboard',
                'subItems' => [
                    ['name' => 'Freight Overview', 'path' => '/dashboard'],
                    ['name' => 'Operations Analytics', 'path' => '/analytics'],
                ],
            ],
            [
                'icon' => 'crm',
                'name' => 'Sales & CRM',
                'subItems' => [
                    ['name' => 'Inquiries', 'path' => '/sales/inquiries'],
                    ['name' => 'Quotations', 'path' => '/sales/quotations'],
                    ['name' => 'Sales Orders', 'path' => '/sales/orders'],
                ],
            ],
            [
                'icon' => 'ship',
                'name' => 'Freight Operations',
                'new' => true,
                'subItems' => [
                    ['name' => 'Shipments (Jobs)', 'path' => '/operations/shipments'],
                    ['name' => 'Bookings', 'path' => '/operations/bookings'],
                    ['name' => 'Ocean Export FCL', 'path' => '/operations/ocean-export'],
                    ['name' => 'Ocean Import FCL', 'path' => '/operations/ocean-import'],
                    ['name' => 'Air Freight', 'path' => '/operations/air-freight'],
                ],
            ],
            [
                'icon' => 'document',
                'name' => 'Documentation',
                'subItems' => [
                    ['name' => 'House B/L & HAWB', 'path' => '/docs/hbl'],
                    ['name' => 'Master B/L & MAWB', 'path' => '/docs/mbl'],
                    ['name' => 'Arrival Notices', 'path' => '/docs/arrival-notices'],
                    ['name' => 'Delivery Orders (D/O)', 'path' => '/docs/delivery-orders'],
                ],
            ],
            [
                'icon' => 'truck',
                'name' => 'Logistics & Haulage',
                'subItems' => [
                    ['name' => 'Transport Orders', 'path' => '/logistics/transport-orders'],
                    ['name' => 'Dispatch & Trucking', 'path' => '/logistics/dispatch'],
                    ['name' => 'Proof of Delivery (POD)', 'path' => '/logistics/pod'],
                ],
            ],
        ];
    }

    public static function getFinanceItems()
    {
        return [
            [
                'icon' => 'billing',
                'name' => 'Shipment Billing',
                'subItems' => [
                    ['name' => 'Charges & Costing', 'path' => '/billing/charges'],
                    ['name' => 'Debit Notes', 'path' => '/billing/debit-notes'],
                    ['name' => 'Credit Notes', 'path' => '/billing/credit-notes'],
                ],
            ],
            [
                'icon' => 'finance',
                'name' => 'Accounting & AR/AP',
                'subItems' => [
                    ['name' => 'Customer Invoices', 'path' => '/accounting/invoices'],
                    ['name' => 'Carrier Payables', 'path' => '/accounting/payables'],
                    ['name' => 'Receipts & Payments', 'path' => '/accounting/payments'],
                ],
            ],
        ];
    }

    public static function getMasterDataItems()
    {
        return [
            [
                'icon' => 'partners',
                'name' => 'Business Partners',
                'subItems' => [
                    ['name' => 'Partner Directory', 'path' => '/master/partners'],
                    ['name' => 'Credit Profiles', 'path' => '/master/credit-profiles'],
                    ['name' => 'Sales Assignments', 'path' => '/master/sales-assignments'],
                ],
            ],
            [
                'icon' => 'reference',
                'name' => 'Tariffs & Reference',
                'subItems' => [
                    ['name' => 'Ports & Airports', 'path' => '/master/ports-airports'],
                    ['name' => 'Charge Codes', 'path' => '/master/charge-codes'],
                    ['name' => 'Exchange Rates', 'path' => '/master/exchange-rates'],
                ],
            ],
            [
                'icon' => 'settings',
                'name' => 'System & Org',
                'subItems' => [
                    ['name' => 'Company & Branches', 'path' => '/system/branches'],
                    ['name' => 'Numbering Sequences', 'path' => '/system/sequences'],
                    ['name' => 'Users & Roles', 'path' => '/system/users'],
                ],
            ],
        ];
    }

    public static function getMenuGroups()
    {
        return [
            [
                'title' => 'Operations',
                'items' => self::getOperationsItems(),
            ],
            [
                'title' => 'Finance & Billing',
                'items' => self::getFinanceItems(),
            ],
            [
                'title' => 'Master Data & System',
                'items' => self::getMasterDataItems(),
            ],
        ];
    }

    public static function isActive($path)
    {
        return request()->is(ltrim($path, '/'));
    }

    public static function getIconSvg($iconName)
    {
        $icons = [
            'dashboard' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z" fill="currentColor"></path></svg>',

            'crm' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/><rect width="20" height="14" x="2" y="6" rx="2"/><path d="M12 11h.01"/></svg>',

            'ship' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 20a4.5 4.5 0 0 0 5 0 4.5 4.5 0 0 1 5 0 4.5 4.5 0 0 0 5 0 4.5 4.5 0 0 1 5 0"/><path d="m4 17 1.5-6h13L20 17"/><path d="M7 11V4h4v7"/><path d="M12 4h4v7"/></svg>',

            'document' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>',

            'truck' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M14 8h4.5a2 2 0 0 1 1.6.8L22 12v5a1 1 0 0 1-1 1h-2"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg>',

            'billing' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/><path d="M14 15h2"/><path d="M8 15h2"/></svg>',

            'finance' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',

            'partners' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',

            'reference' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>',

            'settings' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>',
        ];

        return $icons[$iconName] ?? '<svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor"/></svg>';
    }
}
