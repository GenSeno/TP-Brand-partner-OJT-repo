<template>
    <Head title="My Account" />

    <div class="account-page">
        <div class="account-container">
            <!-- Breadcrumb -->
            <div class="account-breadcrumb">
                <Link
                    :href="
                        route(
                            'store.brand-partner.index',
                            $page.props.brandPartner?.slug,
                        )
                    "
                    >Shop</Link
                >
                <span> &rsaquo; </span>
                <span>My Profile</span>
            </div>

            <div class="account-wrapper">
                <!-- Sidebar -->
                <aside class="account-sidebar">
                    <h3 class="sidebar-title">My Account</h3>
                    <ul class="sidebar-menu">
                        <li class="active">
                            <a href="#">My Profile</a>
                        </li>
                        <li><a href="#">My Orders</a></li>
                        <li><a href="#">My Addresses</a></li>
                        <li>
                            <Link
                                :href="
                                    route(
                                        'store.brand-partner.cart',
                                        $page.props.brandPartner?.slug,
                                    )
                                "
                            >
                                My Cart
                            </Link>
                        </li>
                        <li><a href="#">My Wishlist</a></li>
                        <li>
                            <Link
                                :href="route('store.brand-partner.logout')"
                                method="post"
                                as="button"
                                class="logout-btn"
                            >
                                Logout
                            </Link>
                        </li>
                    </ul>
                </aside>

                <!-- Main Content -->
                <div class="account-content">
                    <h2 class="account-page-title">My Profile</h2>
                    <p class="account-page-subtitle">
                        Manage and protect your account
                    </p>

                    <!-- My Details -->
                    <div class="account-section">
                        <div class="section-header">
                            <h4>My Details</h4>
                        </div>

                        <div class="detail-field">
                            <label>FULL NAME</label>
                            <div class="detail-value">{{ user.name }}</div>
                        </div>

                        <div class="detail-field">
                            <label>EMAIL ADDRESS</label>
                            <div class="detail-value">{{ user.email }}</div>
                        </div>

                        <div class="detail-field">
                            <label>MEMBER SINCE</label>
                            <div class="detail-value">
                                {{ formatDate(user.created_at) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
});

const formatDate = (date) => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>

<style scoped>
.account-page {
    min-height: 60vh;
    padding: 40px 0 60px;
    background: #f9f9f9;
    font-family: 'Public Sans', sans-serif;
    margin-top: 80px;
}

.account-container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 24px;
}

.account-breadcrumb {
    font-size: 13px;
    color: #888;
    margin-bottom: 24px;
}

.account-breadcrumb a {
    color: #888;
    text-decoration: none;
}

.account-breadcrumb a:hover {
    color: #ff9505;
}

.account-wrapper {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 32px;
    align-items: start;
}

/* Sidebar */
.account-sidebar {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #eee;
    padding: 24px;
}

.sidebar-title {
    font-size: 18px;
    font-weight: 800;
    color: #1b1b3e;
    margin: 0 0 20px;
}

.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.sidebar-menu li a,
.sidebar-menu li .logout-btn {
    display: block;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #555;
    text-decoration: none;
    cursor: pointer;
    background: none;
    border: none;
    width: 100%;
    text-align: left;
    font-family: 'Public Sans', sans-serif;
    transition:
        background 0.15s,
        color 0.15s;
}

.sidebar-menu li a:hover,
.sidebar-menu li .logout-btn:hover {
    background: #fff8ee;
    color: #ff9505;
}

.sidebar-menu li.active a {
    background: #1b5e38;
    color: #fff;
    font-weight: 700;
}

/* Main content */
.account-content {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #eee;
    padding: 32px;
}

.account-page-title {
    font-size: 22px;
    font-weight: 800;
    color: #1b1b3e;
    margin: 0 0 4px;
}

.account-page-subtitle {
    font-size: 13px;
    color: #888;
    margin: 0 0 28px;
}

.account-section {
    border-top: 1px solid #f0f0f0;
    padding-top: 20px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h4 {
    font-size: 15px;
    font-weight: 700;
    color: #1b1b3e;
    margin: 0;
}

.detail-field {
    margin-bottom: 16px;
}

.detail-field label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.8px;
    color: #aaa;
    margin-bottom: 6px;
}

.detail-value {
    background: #f5f5f5;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 14px;
    font-weight: 500;
    color: #333;
}

@media (max-width: 768px) {
    .account-wrapper {
        grid-template-columns: 1fr;
    }

    .account-page {
        margin-top: 60px;
    }
}
</style>
