<?php

return [
    'name' => env('APP_NAME', 'FG IT HELPDESK'),
    'logo' => 'images/fg_logo.png',
    'departments' => [
        'Account', 'Creative', 'Human Resource', 'Information Technology',
        'Live Host', 'Management', 'Production Cosmetic', 'Production Food',
        'Public Relation', 'Quality', 'R&D', 'Safety', 'Special Project',
        'Store', 'Technician',
    ],
    'core_categories' => [
        'Account & Password', 'Access & Permission', 'Hardware & Laptops',
        'Software & Applications', 'Network', 'Email', 'Printer',
        'Equipment & Accessories', 'Others',
    ],
    'channels' => ['Portal', 'Phone', 'WhatsApp', 'Email'],
    'device_types' => [
        'Laptop', 'Desktop', 'All-in-One', 'Printer / Scanner',
        'Mobile / Smartphone', 'Tablet', 'Server / Network',
        'Peripherals', 'Others',
    ],
    'operating_systems' => [
        'Windows 11', 'Windows 10', 'Windows Server', 'macOS',
        'Linux', 'iOS', 'Android', 'ChromeOS', 'Others',
    ],
    'sla_defaults' => [
        'Critical' => 7200,    // 2 hours
        'High' => 14400,       // 4 hours
        'Normal' => 86400,     // 24 hours / 1 business day
        'Low' => 172800,       // 48 hours / 2 business days
    ],
    'upload' => [
        'chat_max_size' => 8 * 1024 * 1024,   // 8 MB
        'guide_max_size' => 25 * 1024 * 1024,  // 25 MB
        'chat_allowed_ext' => ['png', 'jpg', 'jpeg'],
    ],
    'modules' => [
        'assets' => env('MODULE_ASSETS_ENABLED', false),
        'approvals' => env('MODULE_APPROVALS_ENABLED', false),
    ],
];
