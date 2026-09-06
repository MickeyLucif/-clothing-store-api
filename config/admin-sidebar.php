<?php

declare(strict_types=1);

return [
    'brand' => env('ADMIN_BRAND', 'Clothing Store'),

    'items' => [
        [
            'type' => 'link',
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
            'icon' => 'bi bi-speedometer2',
            'active' => ['admin.dashboard'],
        ],
        [
            'type' => 'link',
            'label' => 'Roles & Permissions',
            'icon' => 'bi bi-shield-lock',
            'active' => ['admin.roles.*'],
            'route' => 'admin.roles.index',
        ],
    ],
];
