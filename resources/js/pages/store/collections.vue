<template>
    <Head title="Collections" />

    <div class="collections-page">
        <!-- Hero Banner -->
        <section class="collections-hero">
            <div class="collections-hero-bg">
                <img src="/img/img-collection1.png" alt="Collections Hero" />
            </div>
            <div class="collections-hero-overlay"></div>
            <div class="collections-hero-content">
                <div class="breadcrumb">
                    <Breadcrumb :items="breadcrumbItems" />
                </div>
                <h1 class="collections-hero-title">OUR COLLECTION</h1>
                <p class="collections-hero-desc">
                    Tribu Pakaras is launching its official eCommerce platform
                    soon, powered by upgraded production, improved quality, and
                    expanded product offerings designed for athletes who demand
                    more.
                </p>
                <a href="#collections-grid" class="collections-scroll-btn">
                    <i class="ri-arrow-down-s-line"></i>
                </a>
            </div>
        </section>

        <!-- Collections Grid -->
        <section class="collections-grid-section" id="collections-grid">
            <div class="collections-grid-inner">
                <div class="collections-grid">
                    <!-- Collection Item 1 - img-collection2.png -->
                    <div class="collection-item">
                        <div class="collection-item-image">
                            <img
                                src="/img/img-collection2.png"
                                alt="Collection 2"
                            />
                        </div>
                        <div class="collection-item-body">
                            <h3 class="collection-item-title">
                                Running Collection
                            </h3>
                            <p class="collection-item-desc">
                                Pellentesque molestie lectus condimentum vel
                                cras. Magna egestas enim tortor accumsan varius
                                pellentesque amet dolor hac. Lorem sed fermentum
                                sapien facilisi at magna pulvinar pretium.
                            </p>
                            <a href="#" class="collection-item-btn"
                                >VIEW COLLECTION</a
                            >
                        </div>
                    </div>

                    <!-- Collection Item 2 - img-collection3.png -->
                    <div class="collection-item">
                        <div class="collection-item-image">
                            <img
                                src="/img/img-carousel3.png"
                                alt="Collection 3"
                            />
                        </div>
                        <div class="collection-item-body">
                            <h3 class="collection-item-title">
                                Training Collection
                            </h3>
                            <p class="collection-item-desc">
                                Pellentesque molestie lectus condimentum vel
                                cras. Magna egestas enim tortor accumsan varius
                                pellentesque amet dolor hac. Lorem sed fermentum
                                sapien facilisi at magna pulvinar pretium.
                            </p>
                            <a href="#" class="collection-item-btn"
                                >VIEW COLLECTION</a
                            >
                        </div>
                    </div>

                    <!-- Collection Item 3 - img-collection4.png -->
                    <div class="collection-item">
                        <div class="collection-item-image">
                            <img
                                src="/img/img-collection4.png"
                                alt="Collection 4"
                            />
                        </div>
                        <div class="collection-item-body">
                            <h3 class="collection-item-title">
                                Lifestyle Collection
                            </h3>
                            <p class="collection-item-desc">
                                Pellentesque molestie lectus condimentum vel
                                cras. Magna egestas enim tortor accumsan varius
                                pellentesque amet dolor hac. Lorem sed fermentum
                                sapien facilisi at magna pulvinar pretium.
                            </p>
                            <a href="#" class="collection-item-btn"
                                >VIEW COLLECTION</a
                            >
                        </div>
                    </div>

                    <!-- Collection Item 4 - img-collection5.png -->
                    <div class="collection-item">
                        <div class="collection-item-image">
                            <img
                                src="/img/img-collection5.png"
                                alt="Collection 5"
                            />
                        </div>
                        <div class="collection-item-body">
                            <h3 class="collection-item-title">
                                Limited Edition
                            </h3>
                            <p class="collection-item-desc">
                                Pellentesque molestie lectus condimentum vel
                                cras. Magna egestas enim tortor accumsan varius
                                pellentesque amet dolor hac. Lorem sed fermentum
                                sapien facilisi at magna pulvinar pretium.
                            </p>
                            <a href="#" class="collection-item-btn"
                                >VIEW COLLECTION</a
                            >
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/components/breadcrumb/layout-breadcrumb.vue';
import { ref, reactive, onMounted, onBeforeUnmount, computed } from 'vue';

// Breadcrumb items
const breadcrumbItems = computed(() => [
    { label: 'Collections' }
]);
</script>

<style scoped>
.collections-page {
    font-family: 'Public Sans', sans-serif;
    background: #fff;
    min-height: 100vh;
}

/* Breadcrumb Color Override for Collections Page */
.collections-hero-content :deep(.breadcrumb) {
    justify-content: center;
    padding: 0 0 15px;
    margin-bottom: 0;
}

.collections-hero-content :deep(.breadcrumb-item) {
    color: #ffffff;
}

.collections-hero-content :deep(.breadcrumb-item:not(:last-child)::after) {
    color: #ffffff;
}

.collections-hero-content :deep(.breadcrumb-home) {
    color: #ffffff;
}

.collections-hero-content :deep(.breadcrumb-home:hover) {
    color: #FF9505;
    text-decoration: none;
}

.collections-hero-content :deep(.breadcrumb-item a) {
    color: #ffffff;
}

.collections-hero-content :deep(.breadcrumb-item a:hover) {
    color: #FF9505;
    text-decoration: none;
}

.collections-hero-content :deep(.breadcrumb-item.active) {
    color: #ffffff;
}

.collections-hero-content :deep(.breadcrumb-item span) {
    color: #ffffff;
}

/* Hero */
.collections-hero {
    position: relative;
    height: 700px;
    padding-top: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    overflow: hidden;
}

.collections-hero-bg {
    position: absolute;
    inset: 0;
    background: #444;
}

.collections-hero-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.collections-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    z-index: 1;
}

.collections-hero-content {
    position: relative;
    z-index: 2;
    max-width: 600px;
    padding: 0 24px;
}

.collections-hero-title {
    font-size: 52px;
    font-weight: 900;
    color: #fff;
    margin: 0 0 16px;
    letter-spacing: 2px;
    font-family: 'Public Sans', sans-serif;
}

.collections-hero-desc {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.75);
    line-height: 1.7;
    margin: 0 0 24px;
}

.collections-scroll-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: 2px solid rgba(255, 255, 255, 0.5);
    border-radius: 50%;
    color: #fff;
    font-size: 20px;
    text-decoration: none;
    transition: all 0.25s;
}

.collections-scroll-btn:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
}

/* Grid Section */
.collections-grid-section {
    padding: 60px 0 80px;
}

.collections-grid-inner {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 24px;
}

.collections-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px 32px;
}

/* Collection Item */
.collection-item {
    display: flex;
    flex-direction: column;
    border: 1.5px dashed #d0e4ea;
    transition: all 0.3s ease;
}

.collection-item:hover {
    border-color: #3c8599;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

.collection-item-image {
    width: 100%;
    aspect-ratio: 16 / 9;
    overflow: hidden;
}

.collection-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.4s ease;
}

.collection-item:hover .collection-item-image img {
    transform: scale(1.04);
}

.collection-item-body {
    padding: 24px 20px 28px;
}

.collection-item-title {
    font-size: 20px;
    font-weight: 800;
    color: #111;
    margin: 0 0 12px;
    font-family: 'Public Sans', sans-serif;
}

.collection-item-desc {
    font-size: 13px;
    color: #555;
    line-height: 1.7;
    margin: 0 0 20px;
}

.collection-item-btn {
    display: inline-block;
    border: 1.5px solid #111;
    color: #111;
    padding: 9px 20px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    text-decoration: none;
    transition: all 0.25s;
}

.collection-item-btn:hover {
    background: #111;
    color: #fff;
}

/* Responsive */
@media (max-width: 768px) {
    .collections-hero {
        height: 350px;
    }

    .collections-hero-title {
        font-size: 36px;
    }

    .collections-grid {
        grid-template-columns: 1fr;
        gap: 32px;
    }

    .collection-item-body {
        padding: 20px 16px 24px;
    }
}

@media (max-width: 480px) {
    .collections-hero-title {
        font-size: 28px;
    }

    .collections-hero-desc {
        font-size: 12px;
    }
}
</style>
