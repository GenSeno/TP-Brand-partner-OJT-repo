<?php

return [
    // Settings Module
    'settings' => [
        'title' => 'Settings',
        'description' => 'Access to system settings and configuration',
    ],
    'settings:manage-staff' => [
        'title' => 'Manage Staff',
        'description' => 'Create, edit, and delete staff members',
    ],
    'settings:manage-roles' => [
        'title' => 'Manage Roles',
        'description' => 'Create, edit, and delete roles and assign permissions',
    ],
    'settings:manage-users' => [
        'title' => 'Manage Users',
        'description' => 'Create, edit, and delete user accounts',
    ],

    // Catalog Module
    'catalog' => [
        'title' => 'Catalog',
        'description' => 'Access to product catalog management',
    ],
    'catalog:manage-products' => [
        'title' => 'Manage Products',
        'description' => 'Create, edit, and delete products and variants',
    ],
    'catalog:manage-categories' => [
        'title' => 'Manage Categories',
        'description' => 'Create, edit, and delete product categories',
    ],
    'catalog:manage-product-options' => [
        'title' => 'Manage Product Options',
        'description' => 'Create, edit, and delete product options (sizes, colors, etc.)',
    ],

    // Sales Module
    'sales' => [
        'title' => 'Sales',
        'description' => 'Access to sales and customer management',
    ],
    'sales:manage-orders' => [
        'title' => 'Manage Orders',
        'description' => 'View, create, edit, and process customer orders',
    ],
    'sales:manage-customers' => [
        'title' => 'Manage Customers',
        'description' => 'Create, edit, and manage customer information',
    ],
    'sales:manage-quotes' => [
        'title' => 'Manage Quotes',
        'description' => 'Create, edit, and manage customer quotations',
    ],

    // Job Orders Module
    'job-orders' => [
        'title' => 'Job Orders',
        'description' => 'Access to job order management',
    ],
    'job-orders:manage-notes' => [
        'title' => 'Manage Notes',
        'description' => 'Add, edit, and delete internal notes on job orders',
    ],
    'job-orders:manage-deadlines' => [
        'title' => 'Manage Deadlines',
        'description' => 'Set and update job order deadlines and due dates',
    ],
    'job-orders:manage-urgency' => [
        'title' => 'Manage Urgency',
        'description' => 'Change job order urgency flags (Rush, Priority, Normal)',
    ],
    'job-orders:manage-new-order-tasks' => [
        'title' => 'Manage New Order Tasks',
        'description' => 'Handle tasks and operations for new job orders',
    ],
    'job-orders:manage-artist-tasks' => [
        'title' => 'Manage Artist Tasks',
        'description' => 'Handle tasks and operations in the artist stage',
    ],
    'job-orders:manage-printing-tasks' => [
        'title' => 'Manage Printing Tasks',
        'description' => 'Handle tasks and operations in the printing stage',
    ],
    'job-orders:manage-heat-press-tasks' => [
        'title' => 'Manage Heat Press Tasks',
        'description' => 'Handle tasks and operations in the heat-press/cutting stage',
    ],
    'job-orders:manage-sewing-tasks' => [
        'title' => 'Manage Sewing Tasks',
        'description' => 'Handle tasks and operations in the sewing stage',
    ],
    'job-orders:manage-packing-tasks' => [
        'title' => 'Manage Packing Tasks',
        'description' => 'Handle tasks and operations in the packing stage',
    ],
    'job-orders:manage-dispatching-tasks' => [
        'title' => 'Manage Dispatching Tasks',
        'description' => 'Handle tasks and operations in the dispatching stage',
    ],
    'job-orders:manage-completed-tasks' => [
        'title' => 'Manage Completed Tasks',
        'description' => 'Handle tasks and operations for completed job orders',
    ],

    // Operations Module
    'operations' => [
        'title' => 'Operations',
        'description' => 'Access to operations and inventory management',
    ],
    'operations:manage-raw-materials' => [
        'title' => 'Manage Raw Materials',
        'description' => 'Create, edit, and manage raw material inventory',
    ],
    'operations:manage-purchase-orders' => [
        'title' => 'Manage Purchase Orders',
        'description' => 'Create, edit, and process purchase orders for supplies',
    ],
];
