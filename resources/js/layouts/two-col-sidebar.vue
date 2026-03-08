<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <!-- Two Col Sidebar -->
    <div class="two-col-sidebar" id="two-col-sidebar">
        <div class="sidebar sidebar-twocol">
            <div class="twocol-mini">
                <div class="sidebar-left slimscroll">
                    <div
                        class="nav flex-column align-items-center nav-pills"
                        id="sidebar-tabs"
                        role="tablist"
                        aria-orientation="vertical"
                    >
                        <a
                            href="#"
                            class="nav-link active"
                            title="Dashboard"
                            data-bs-toggle="tab"
                            data-bs-target="#dashboard"
                        >
                            <i class="ti ti-smart-home"></i>
                        </a>
                        <a
                            href="#"
                            class="nav-link"
                            title="Super Admin"
                            data-bs-toggle="tab"
                            data-bs-target="#super-admin"
                        >
                            <i class="ti ti-user-star"></i>
                        </a>
                        <a
                            href="#"
                            class="nav-link"
                            title="Apps"
                            data-bs-toggle="tab"
                            data-bs-target="#application"
                        >
                            <i class="ti ti-layout-grid-add"></i>
                        </a>
                        <a
                            href="#"
                            class="nav-link"
                            title="Layout"
                            data-bs-toggle="tab"
                            data-bs-target="#layout"
                        >
                            <i class="ti ti-layout-board-split"></i>
                        </a>
                        <a
                            href="#"
                            class="nav-link"
                            title="Inventory"
                            data-bs-toggle="tab"
                            data-bs-target="#inventory"
                        >
                            <i class="ti ti-table-plus"></i>
                        </a>
                        <a
                            href="#"
                            class="nav-link"
                            title="Stock"
                            data-bs-toggle="tab"
                            data-bs-target="#stock"
                        >
                            <i class="ti ti-stack-3"></i>
                        </a>
                        <a
                            href="#"
                            class="nav-link"
                            title="Sales"
                            data-bs-toggle="tab"
                            data-bs-target="#sales"
                        >
                            <i class="ti ti-device-laptop"></i>
                        </a>
                        <a
                            href="#"
                            class="nav-link"
                            title="Finance"
                            data-bs-toggle="tab"
                            data-bs-target="#finance"
                        >
                            <i class="ti ti-shopping-cart-dollar"></i>
                        </a>
                        <a
                            href="#"
                            class="nav-link"
                            title="Hrm"
                            data-bs-toggle="tab"
                            data-bs-target="#hrm"
                        >
                            <i class="ti ti-cash"></i>
                        </a>
                        <a
                            href="#"
                            class="nav-link"
                            title="Reports"
                            data-bs-toggle="tab"
                            data-bs-target="#reports"
                        >
                            <i class="ti ti-license"></i>
                        </a>
                        <a
                            href="#"
                            class="nav-link"
                            title="Pages"
                            data-bs-toggle="tab"
                            data-bs-target="#pages"
                        >
                            <i class="ti ti-page-break"></i>
                        </a>
                        <a
                            href="#"
                            class="nav-link"
                            title="Settings"
                            data-bs-toggle="tab"
                            data-bs-target="#settings"
                        >
                            <i class="ti ti-lock-check"></i>
                        </a>
                        <a
                            href="#"
                            class="nav-link"
                            title="UI Elements"
                            data-bs-toggle="tab"
                            data-bs-target="#ui-elements"
                        >
                            <i class="ti ti-ux-circle"></i>
                        </a>
                        <a
                            href="#"
                            class="nav-link"
                            title="Extras"
                            data-bs-toggle="tab"
                            data-bs-target="#extras"
                        >
                            <i class="ti ti-vector-triangle"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="sidebar-right">
                <div class="sidebar-scroll">
                    <div
                        class="text-center rounded bg-light p-3 mb-3 user-profile"
                    >
                        <div class="avatar avatar-lg online mb-3">
                            <img
                                src="/img/customer/customer15.jpg"
                                alt="Img"
                                class="img-fluid rounded-circle"
                            />
                        </div>
                        <h6 class="fs-12 fw-bold mb-1">Adrian Herman</h6>
                        <p class="fs-10 mb-0">System Admin</p>
                    </div>
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="dashboard">
                            <ul>
                                <li class="menu-title"><span>MAIN</span></li>
                                <li>
                                    <Link href="/dashboard/" class="active"
                                        >Admin Dashboard</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/dashboard/admin-dashboard-2"
                                        >Admin Dashboard 2</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/dashboard/sales-dashboard"
                                        >Sales Dashboard</Link
                                    >
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="super-admin">
                            <ul>
                                <li class="menu-title">
                                    <span>SUPER ADMIN</span>
                                </li>
                                <li>
                                    <Link href="/super-admin/dashboard"
                                        >Dashboard</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/super-admin/companies"
                                        >Companies</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/super-admin/subscription"
                                        >Subscriptions</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/super-admin/packages"
                                        >Packages</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/super-admin/domain"
                                        >Domain</Link
                                    >
                                </li>
                                <li>
                                    <Link
                                        href="/super-admin/purchase-transaction"
                                        >Purchase Transaction</Link
                                    >
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="application">
                            <ul>
                                <li>
                                    <Link href="/application/chat">Chat</Link>
                                </li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);"
                                        >Call<span
                                            class="menu-arrow inside-submenu"
                                        ></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link href="/call/video-call"
                                                >Video Call</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/call/audio-call"
                                                >Audio Call</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/call/call-history"
                                                >Call History</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <Link href="/application/calendar"
                                        >Calendar</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/application/contacts"
                                        >Contacts</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/application/email">Email</Link>
                                </li>
                                <li>
                                    <Link href="/application/todo">To Do</Link>
                                </li>
                                <li>
                                    <Link href="/application/notes">Notes</Link>
                                </li>
                                <li>
                                    <Link href="/application/file-manager"
                                        >File Manager</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/application/projects"
                                        >Projects</Link
                                    >
                                </li>
                                <li class="submenu submenu-two">
                                    <a href="javascript:void(0);"
                                        >Ecommerce<span
                                            class="menu-arrow inside-submenu"
                                        ></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link href="/ecommerce/products"
                                                >Products</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/ecommerce/orders"
                                                >Orders</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/ecommerce/customers"
                                                >Customers</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/ecommerce/cart"
                                                >Cart</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/ecommerce/checkout"
                                                >Checkout</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/ecommerce/wishlist"
                                                >Wishlist</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/ecommerce/reviews"
                                                >Reviews</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <Link href="/application/social-feed"
                                        >Social Feed</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/application/search-list"
                                        >Search List</Link
                                    >
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="layout">
                            <ul>
                                <li class="menu-title"><span>LAYOUT</span></li>
                                <li>
                                    <Link href="/layouts/layout-horizontal"
                                        >Horizontal</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/layouts/layout-detached"
                                        >Detached</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/layouts/layout-modern"
                                        >Modern</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/layouts/layout-two-column"
                                        >Two Column</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/layouts/layout-hovered"
                                        >Hovered</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/layouts/layout-boxed"
                                        >Boxed</Link
                                    >
                                </li>
                                <li>
                                    <Link href="/layouts/layout-rtl">RTL</Link>
                                </li>
                                <li>
                                    <Link href="/layouts/layout-dark"
                                        >Dark</Link
                                    >
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="inventory">
                            <ul>
                                <li class="menu-title">
                                    <span>Inventory</span>
                                </li>
                                <li>
                                    <Link href="/inventory/product-list"
                                        ><span>Products</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/inventory/add-product"
                                        ><span>Create Product</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/inventory/expired-products"
                                        ><span>Expired Products</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/inventory/low-stocks"
                                        ><span>Low Stocks</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/inventory/category-list"
                                        ><span>Category</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/inventory/sub-categories"
                                        ><span>Sub Category</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/inventory/brand-list"
                                        ><span>Brands</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/inventory/units"
                                        ><span>Units</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/inventory/varriant-attributes"
                                        ><span>Variant Attributes</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/inventory/warranty"
                                        ><span>Warranties</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/inventory/barcode"
                                        ><span>Print Barcode</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/inventory/qrcode"
                                        ><span>Print QR Code</span></Link
                                    >
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="stock">
                            <ul>
                                <li class="menu-title"><span>Stock</span></li>
                                <li>
                                    <Link href="/stock/manage-stocks"
                                        ><span>Manage Stock</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/stock/stock-adjustment"
                                        ><span>Stock Adjustment</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/stock/stock-transfer"
                                        ><span>Stock Transfer</span></Link
                                    >
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="sales">
                            <ul>
                                <li class="menu-title"><span>Sales</span></li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Sales</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link href="/sales/online-orders"
                                                >Online Orders</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/sales/pos-orders"
                                                >POS Orders</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <Link href="/sales/invoice"
                                        ><span>Invoices</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/sales/sales-returns"
                                        ><span>Sales Return</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/sales/quotation-list"
                                        ><span>Quotation</span></Link
                                    >
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>POS</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link href="/pos/pos-1">POS 1</Link>
                                        </li>
                                        <li>
                                            <Link href="/pos/pos-2">POS 2</Link>
                                        </li>
                                        <li>
                                            <Link href="/pos/pos-3">POS 3</Link>
                                        </li>
                                        <li>
                                            <Link href="/pos/pos-4">POS 4</Link>
                                        </li>
                                        <li>
                                            <Link href="/pos/pos-5">POS 5</Link>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="finance">
                            <ul>
                                <li class="menu-title">
                                    <span>FINANCE & ACCOUNTS</span>
                                </li>
                                <li>
                                    <Link href="/coupons"
                                        ><span>Coupons</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/gift-cards"
                                        ><span>Gift Cards</span></Link
                                    >
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Discount</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link href="/discount/discount-plan"
                                                >Discount Plan</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/discount/discount-list"
                                                >Discount</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <Link href="/purchases/purchase-list"
                                        ><span>Purchases</span></Link
                                    >
                                </li>
                                <li>
                                    <Link
                                        href="/purchases/purchase-order-report"
                                        ><span>Purchase Order</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/purchases/purchase-returns"
                                        ><span>Purchase Return</span></Link
                                    >
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Expenses</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link href="/expenses/expenses-list"
                                                >Expenses</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/expenses/expenses-category"
                                                >Expense Category</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Income</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link href="/income/income-list"
                                                >Income</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/income/income-category"
                                                >Income Category</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <Link href="/finance/account-list"
                                        ><span>Bank Accounts</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/finance/money-transfer"
                                        ><span>Money Transfer</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/finance/balance-sheet"
                                        ><span>Balance Sheet</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/finance/trial-balance"
                                        ><span>Trial Balance</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/finance/cash-flow"
                                        ><span>Cash Flow</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/finance/account-statement"
                                        ><span>Account Statement</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/ecommerce/customers"
                                        ><span>Customers</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/people/billers"
                                        ><span>Billers</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/people/suppliers"
                                        ><span>Suppliers</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/people/store-list"
                                        ><span>Stores</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/people/warehouse"
                                        ><span>Warehouses</span></Link
                                    >
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="hrm">
                            <ul>
                                <li class="menu-title"><span>Hrm</span></li>
                                <li>
                                    <Link href="/hrm/employees-grid"
                                        ><span>Employees</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/hrm/department-grid"
                                        ><span>Departments</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/hrm/designation"
                                        ><span>Designation</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/hrm/shift"
                                        ><span>Shifts</span></Link
                                    >
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Attendence</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link
                                                href="/hrm/attendance-employee"
                                                >Employee</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/hrm/attendance-admin"
                                                >Admin</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Leaves</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link href="/leaves/leaves-admin"
                                                >Admin Leaves</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/leaves/leaves-employee"
                                                >Employee Leaves</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/leaves/leave-types"
                                                >Leave Types</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <Link href="/hrm/holidays"
                                        ><span>Holidays</span></Link
                                    >
                                </li>
                                <li class="submenu">
                                    <Link href="/payroll/employee-salary"
                                        ><span>Payroll</span
                                        ><span class="menu-arrow"></span
                                    ></Link>
                                    <ul>
                                        <li>
                                            <Link
                                                href="/payroll/employee-salary"
                                                >Employee Salary</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/payroll/payslip"
                                                >Payslip</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="reports">
                            <ul>
                                <li class="menu-title"><span>Reports</span></li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Sales Report</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link
                                                href="/sales-report/sales-report"
                                                >Sales Report</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/sales-report/best-sellers"
                                                >Best Seller</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <Link href="/reports/purchase-report"
                                        ><span>Purchase report</span></Link
                                    >
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Inventory Report</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link
                                                href="/inventory-report/inventory-report"
                                                >Inventory Report</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/inventory-report/stock-history"
                                                >Stock History</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/inventory-report/sold-stock"
                                                >Sold Stock</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <Link href="/reports/invoice-report"
                                        ><span>Invoice Report</span></Link
                                    >
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Supplier Report</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link
                                                href="/supplier-report/supplier-report"
                                                >Supplier Report</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/supplier-report/supplier-due-report"
                                                >Supplier Due Report</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Customer Report</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link
                                                href="/customer-report/customer-report"
                                                >Customer Report</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/customer-report/customer-due-report"
                                                >Customer Due Report</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Product Report</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link
                                                href="/product-report/product-report"
                                                >Product Report</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/product-report/product-expiry-report"
                                                >Product Expiry Report</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/product-report/product-quantity-alert"
                                                >Product Quantity Alert</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <Link href="/reports/expense-report"
                                        ><span>Expense Report</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/reports/income-report"
                                        ><span>Income Report</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/reports/tax-report"
                                        ><span>Tax Report</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/reports/profit-and-loss"
                                        ><span>Profit & Loss</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/reports/annual-report"
                                        ><span>Annual Report</span></Link
                                    >
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="pages">
                            <ul>
                                <li class="menu-title"><span>Pages</span></li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Pages</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link href="/page/pages"
                                                >Pages</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Blog</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link href="/blog/all-blog"
                                                >All Blog</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/blog/blog-tag"
                                                >Blog Tags</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/blog/blog-categories"
                                                >Categories</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/blog/blog-comments"
                                                >Blog Comments</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Location</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link href="/location/countries"
                                                >Countries</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/location/states"
                                                >States</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/location/cities"
                                                >Cities</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <Link href="/content/testimonials"
                                        ><span>Testimonials</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/content/faq"
                                        ><span>FAQ</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/users/users-list"
                                        ><span>Users</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/users/roles-permissions"
                                        ><span>Roles & Permissions</span></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/users/delete-account"
                                        ><span
                                            >Delete Account Request</span
                                        ></Link
                                    >
                                </li>
                                <li>
                                    <Link href="/pages/profile"
                                        ><span>Profile</span></Link
                                    >
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Authentication</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li class="submenu submenu-two">
                                            <a href="javascript:void(0);"
                                                >Login<span
                                                    class="menu-arrow inside-submenu"
                                                ></span
                                            ></a>
                                            <ul>
                                                <li>
                                                    <Link href="/">Cover</Link>
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/authentication/signin-2"
                                                        >Illustration</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/authentication/signin-3"
                                                        >Basic</Link
                                                    >
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="submenu submenu-two">
                                            <a href="javascript:void(0);"
                                                >Register<span
                                                    class="menu-arrow inside-submenu"
                                                ></span
                                            ></a>
                                            <ul>
                                                <li>
                                                    <Link href="/register"
                                                        >Cover</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/authentication/register-2"
                                                        >Illustration</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/authentication/register-3"
                                                        >Basic</Link
                                                    >
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="submenu submenu-two">
                                            <a href="javascript:void(0);"
                                                >Forgot Password<span
                                                    class="menu-arrow inside-submenu"
                                                ></span
                                            ></a>
                                            <ul>
                                                <li>
                                                    <Link
                                                        href="/forgot-password"
                                                        >Cover</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/authentication/forgot-password-2"
                                                        >Illustration</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/authentication/forgot-password-3"
                                                        >Basic</Link
                                                    >
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="submenu submenu-two">
                                            <a href="javascript:void(0);"
                                                >Reset Password<span
                                                    class="menu-arrow inside-submenu"
                                                ></span
                                            ></a>
                                            <ul>
                                                <li>
                                                    <Link href="/reset-password"
                                                        >Cover</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/authentication/reset-password-2"
                                                        >Illustration</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/authentication/reset-password-3"
                                                        >Basic</Link
                                                    >
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="submenu submenu-two">
                                            <a href="javascript:void(0);"
                                                >Email Verification<span
                                                    class="menu-arrow inside-submenu"
                                                ></span
                                            ></a>
                                            <ul>
                                                <li>
                                                    <Link
                                                        href="/email-verification"
                                                        >Cover</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/authentication/email-verification-2"
                                                        >Illustration</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/authentication/email-verification-3"
                                                        >Basic</Link
                                                    >
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="submenu submenu-two">
                                            <a href="javascript:void(0);"
                                                >2 Step Verification<span
                                                    class="menu-arrow inside-submenu"
                                                ></span
                                            ></a>
                                            <ul>
                                                <li>
                                                    <Link
                                                        href="/two-step-verification"
                                                        >Cover</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/authentication/two-step-verification-2"
                                                        >Illustration</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/authentication/two-step-verification-3"
                                                        >Basic</Link
                                                    >
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <Link
                                                href="/authentication/lock-screen"
                                                >Lock Screen</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Error Pages</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link href="/error/error-404"
                                                >404 Error
                                            </Link>
                                        </li>
                                        <li>
                                            <Link href="/error/error-500"
                                                >500 Error
                                            </Link>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <Link href="/pages/blank-page"
                                        ><span>Blank Page</span>
                                    </Link>
                                </li>
                                <li>
                                    <Link href="/pages/pricing"
                                        ><span>Pricing</span>
                                    </Link>
                                </li>
                                <li>
                                    <Link href="/pages/coming-soon"
                                        ><span>Coming Soon</span>
                                    </Link>
                                </li>
                                <li>
                                    <Link href="/pages/under-maintenance"
                                        ><span>Under Maintenance</span>
                                    </Link>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="settings">
                            <ul>
                                <li class="menu-title">
                                    <span>Settings</span>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>General Settings</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link
                                                href="/settings/general-settings"
                                                >Profile</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/settings/security-settings"
                                                >Security</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/settings/notification"
                                                >Notifications</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/settings/connected-apps"
                                                >Connected Apps</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Website Settings</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link
                                                href="/website-settings/system-settings"
                                                >System Settings</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/website-settings/company-settings"
                                                >Company Settings
                                            </Link>
                                        </li>
                                        <li>
                                            <Link
                                                href="/website-settings/localization-settings"
                                                >Localization</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/website-settings/prefixes"
                                                >Prefixes</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/website-settings/preference"
                                                >Preference</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/website-settings/appearance"
                                                >Appearance</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/website-settings/social-authentication"
                                                >Social Authentication</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/website-settings/language-settings"
                                                >Language</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <span>App Settings</span
                                        ><span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li class="submenu submenu-two">
                                            <a href="javascript:void(0);"
                                                >Invoice<span
                                                    class="menu-arrow inside-submenu"
                                                ></span
                                            ></a>
                                            <ul>
                                                <li>
                                                    <Link
                                                        href="/app-settings/invoice-settings"
                                                        >Invoice Settings</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/app-settings/invoice-templates"
                                                        >Invoice Template</Link
                                                    >
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <Link
                                                href="/app-settings/printer-settings"
                                                >Printer</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/app-settings/pos-settings"
                                                >POS</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/app-settings/custom-fields"
                                                >Custom Fields</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <span>System Settings</span
                                        ><span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li class="submenu submenu-two">
                                            <a href="javascript:void(0);"
                                                >Email<span
                                                    class="menu-arrow inside-submenu"
                                                ></span
                                            ></a>
                                            <ul>
                                                <li>
                                                    <Link
                                                        href="/system-settings/email-settings"
                                                        >Email Settings</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/system-settings/email-templates"
                                                        >Email Template</Link
                                                    >
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="submenu submenu-two">
                                            <a href="javascript:void(0);"
                                                >SMS<span
                                                    class="menu-arrow inside-submenu"
                                                ></span
                                            ></a>
                                            <ul>
                                                <li>
                                                    <Link
                                                        href="/system-settings/sms-settings"
                                                        >SMS Settings</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/system-settings/sms-templates"
                                                        >SMS Template</Link
                                                    >
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <Link
                                                href="/system-settings/otp-settings"
                                                >OTP</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/system-settings/gdpr-settings"
                                                >GDPR Cookies</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <span>Financial Settings</span
                                        ><span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li>
                                            <Link
                                                href="/financial-settings/payment-gateway-settings"
                                                >Payment Gateway</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/financial-settings/bank-settings-grid"
                                                >Bank Accounts</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/financial-settings/tax-rates"
                                                >Tax Rates</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/financial-settings/currency-settings"
                                                >Currencies</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <span>Other Settings</span
                                        ><span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li>
                                            <Link
                                                href="/other-settings/storage-settings"
                                                >Storage</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/other-settings/ban-ip-address"
                                                >Ban IP Address</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <Link href="/"><span>Logout</span> </Link>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="ui-elements">
                            <ul>
                                <li class="menu-title">
                                    <span>Ui Interface</span>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <span>Base UI</span
                                        ><span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li>
                                            <Link href="/baseui/ui-alerts"
                                                >Alerts</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-accordion"
                                                >Accordion</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-avatar"
                                                >Avatar</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-badges"
                                                >Badges</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-borders"
                                                >Border</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-buttons"
                                                >Buttons</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/baseui/ui-buttons-group"
                                                >Button Group</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-breadcrumb"
                                                >Breadcrumb</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-cards"
                                                >Card</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-carousel"
                                                >Carousel</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-colors"
                                                >Colors</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-dropdowns"
                                                >Dropdowns</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-grid"
                                                >Grid</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-images"
                                                >Images</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-lightbox"
                                                >Lightbox</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-media"
                                                >Media</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-modals"
                                                >Modals</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-offcanvas"
                                                >Offcanvas</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-pagination"
                                                >Pagination</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-popovers"
                                                >Popovers</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-progress"
                                                >Progress</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-placeholders"
                                                >Placeholders</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-spinner"
                                                >Spinner</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-tooltips"
                                                >Tooltips</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-typography"
                                                >Typography</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/baseui/ui-video"
                                                >Video</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <span>Advanced UI</span
                                        ><span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li>
                                            <Link href="/advancedui/ui-ribbon"
                                                >Ribbon</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/advancedui/ui-clipboard"
                                                >Clipboard</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/advancedui/ui-drag-drop"
                                                >Drag & Drop</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/advancedui/ui-rating"
                                                >Rating</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/advancedui/ui-text-editor"
                                                >Text Editor</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/advancedui/ui-counter"
                                                >Counter</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/advancedui/ui-scrollbar"
                                                >Scrollbar</Link
                                            >
                                        </li>
                                        <li>
                                            <Link
                                                href="/advancedui/ui-stickynote"
                                                >Sticky Note</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/advancedui/ui-timeline"
                                                >Timeline</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <span>Charts</span
                                        ><span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li>
                                            <Link href="/charts/chart-apex"
                                                >Apex Charts</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/charts/chart-c3"
                                                >Chart C3</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/charts/chart-js"
                                                >Chart Js</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/charts/chart-morris"
                                                >Morris Charts</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/charts/chart-flot"
                                                >Flot Charts</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <span>Icons</span
                                        ><span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li>
                                            <Link href="/icons/icon-fontawesome"
                                                >Fontawesome Icons</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/icons/icon-feather"
                                                >Feather Icons</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/icons/icon-ionic"
                                                >Ionic Icons</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/icons/icon-material"
                                                >Material Icons</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/icons/icon-pe7"
                                                >Pe7 Icons</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/icons/icon-weather"
                                                >Weather Icons</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/icons/icon-typicon"
                                                >Typicon Icons</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/icons/icon-flag"
                                                >Flag Icons</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <span>Forms</span
                                        ><span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li class="submenu submenu-two">
                                            <a href="javascript:void(0);"
                                                >Form Elements<span
                                                    class="menu-arrow inside-submenu"
                                                ></span
                                            ></a>
                                            <ul>
                                                <li>
                                                    <Link
                                                        href="/forms/form-basic-inputs"
                                                        >Basic Inputs</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/forms/form-checkbox-radios"
                                                        >Checkbox & Radios</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/forms/form-input-groups"
                                                        >Input Groups</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/forms/form-grid-gutters"
                                                        >Grid & Gutters</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/forms/form-select"
                                                        >Form Select</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/forms/form-mask"
                                                        >Input Masks</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/forms/form-fileupload"
                                                        >File Uploads</Link
                                                    >
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="submenu submenu-two">
                                            <a href="javascript:void(0);"
                                                >Layouts<span
                                                    class="menu-arrow inside-submenu"
                                                ></span
                                            ></a>
                                            <ul>
                                                <li>
                                                    <Link
                                                        href="/forms/form-horizontal"
                                                        >Horizontal Form</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/forms/form-vertical"
                                                        >Vertical Form</Link
                                                    >
                                                </li>
                                                <li>
                                                    <Link
                                                        href="/forms/form-floating-labels"
                                                        >Floating Labels</Link
                                                    >
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <Link href="/forms/form-validation"
                                                >Form Validation</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/forms/form-select2"
                                                >Select2</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/forms/form-wizard"
                                                >Form Wizard</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/forms/form-pickers"
                                                >Form Picker</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Tables</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link href="/tables/tables-basic"
                                                >Basic Tables
                                            </Link>
                                        </li>
                                        <li>
                                            <Link href="/tables/data-tables"
                                                >Data Table
                                            </Link>
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Maps</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <Link href="/maps/maps-vector"
                                                >Vector</Link
                                            >
                                        </li>
                                        <li>
                                            <Link href="/maps/maps-leaflet"
                                                >Leaflet</Link
                                            >
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="extras">
                            <ul>
                                <li class="menu-title"><span>Help</span></li>
                                <li>
                                    <a href="javascript:void(0);"
                                        ><span>Documentation</span></a
                                    >
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        ><span>Changelog v2.0.9</span></a
                                    >
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        ><span>Multi Level</span
                                        ><span class="menu-arrow"></span
                                    ></a>
                                    <ul>
                                        <li>
                                            <a href="javascript:void(0);"
                                                >Level 1.1</a
                                            >
                                        </li>
                                        <li class="submenu submenu-two">
                                            <a href="javascript:void(0);"
                                                >Level 1.2<span
                                                    class="menu-arrow inside-submenu"
                                                ></span
                                            ></a>
                                            <ul>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        >Level 2.1</a
                                                    >
                                                </li>
                                                <li
                                                    class="submenu submenu-two submenu-three"
                                                >
                                                    <a
                                                        href="javascript:void(0);"
                                                        >Level 2.2<span
                                                            class="menu-arrow inside-submenu inside-submenu-two"
                                                        ></span
                                                    ></a>
                                                    <ul>
                                                        <li>
                                                            <a
                                                                href="javascript:void(0);"
                                                                >Level 3.1</a
                                                            >
                                                        </li>
                                                        <li>
                                                            <a
                                                                href="javascript:void(0);"
                                                                >Level 3.2</a
                                                            >
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Two Col Sidebar -->
</template>
