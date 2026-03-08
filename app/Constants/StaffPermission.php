<?php

namespace App\Constants;

use App\Constants\Traits\HasGroups;

/**
 * This contains all the permission constants used throughout the application
 * for staff access control and authorization.
 *
 * Note: Add appropriate permissions as needed. You may run the PermissionSeeder
 * to seed these permissions into the database.
 */
class StaffPermission extends BaseConstant
{
    use HasGroups;

    public const SETTINGS = 'settings';
    public const MANAGE_STAFF = 'settings:manage-staff';
    public const MANAGE_ROLES = 'settings:manage-roles';
    public const MANAGE_USERS = 'settings:manage-users';
    public const MANAGE_EMPLOYEES = 'settings:manage-employees';

    public const CATALOG = 'catalog';
    public const MANAGE_PRODUCTS = 'catalog:manage-products';
    public const MANAGE_CATEGORIES = 'catalog:manage-categories';
    public const MANAGE_PRODUCT_OPTIONS = 'catalog:manage-product-options';
    public const MANAGE_SUPPLIERS = 'catalog:manage-suppliers';

    public const SALES = 'sales';
    public const MANAGE_ORDERS = 'sales:manage-orders';
    public const MANAGE_CUSTOMERS = 'sales:manage-customers';
    public const MANAGE_QUOTES = 'sales:manage-quotes';
    public const MANAGE_INVOICES = 'sales:manage-invoices';

    public const JOB_ORDERS = 'job-orders';
    public const MANAGE_JO_NOTES = 'job-orders:manage-notes';
    public const MANAGE_JO_DEADLINES = 'job-orders:manage-deadlines';
    public const MANAGE_JO_URGENCY = 'job-orders:manage-urgency';
    public const MANAGE_JO_NEW_ORDER_TASKS = 'job-orders:manage-new-order-tasks';
    public const MANAGE_JO_ARTIST_TASKS = 'job-orders:manage-artist-tasks';
    public const MANAGE_JO_APPROVAL_TASKS = 'job-orders:manage-approval-tasks';
    public const MANAGE_JO_PRINTING_TASKS = 'job-orders:manage-printing-tasks';
    public const MANAGE_JO_HEAT_PRESS_TASKS = 'job-orders:manage-heat-press-tasks';
    public const MANAGE_JO_SEWING_TASKS = 'job-orders:manage-sewing-tasks';
    public const MANAGE_JO_PACKING_TASKS = 'job-orders:manage-packing-tasks';
    public const MANAGE_JO_DISPATCHING_TASKS = 'job-orders:manage-dispatching-tasks';
    public const MANAGE_JO_COMPLETED_TASKS = 'job-orders:manage-completed-tasks';
    public const MANAGE_JO_CANCELLED_TASKS = 'job-orders:manage-cancelled-tasks';

    public const OPERATIONS = 'operations';
    public const MANAGE_RAW_MATERIALS = 'operations:manage-raw-materials';
    public const MANAGE_PURCHASE_ORDERS = 'operations:manage-purchase-orders';
}
