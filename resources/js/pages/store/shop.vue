<template>
    <Head title="Our Products" />

    <div class="shop-page">

        <div class="shop-inner">
            <h1 class="shop-title">Our Products</h1>

            <!-- Toolbar -->
            <div class="shop-toolbar">
                <div class="shop-toolbar-left">
                    <button class="toolbar-btn" @click="showFilters = !showFilters">
                        <i class="ri-filter-3-line"></i> Filter
                    </button>
                    <button class="toolbar-btn">
                        <i class="ri-sort-asc"></i> Sort
                    </button>
                    <span class="toolbar-featured">Featured</span>
                </div>
            </div>

            <div class="shop-layout">

                <!-- Sidebar Filters -->
                <aside class="shop-sidebar" :class="{ open: showFilters }">

                    <!-- Categories -->
                    <div class="filter-group">
                        <div class="filter-group-header" @click="toggleGroup('categories')">
                            <span>CATEGORIES</span>
                            <i :class="openGroups.categories ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>
                        </div>
                        <div class="filter-group-body" v-show="openGroups.categories">
                            <label class="filter-radio" v-for="cat in sampleCategories" :key="cat.id">
                                <input type="radio" name="category" :value="cat.id" v-model="selectedCategory">
                                <span>{{ cat.name }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Color -->
                    <div class="filter-group">
                        <div class="filter-group-header" @click="toggleGroup('colors')">
                            <span>COLOR</span>
                            <i :class="openGroups.colors ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>
                        </div>
                        <div class="filter-group-body" v-show="openGroups.colors">
                            <div class="color-swatches">
                                <button
                                    v-for="color in sampleColors"
                                    :key="color.value"
                                    class="color-swatch"
                                    :style="{ background: color.hex }"
                                    :class="{ active: selectedColors.includes(color.value) }"
                                    @click="toggleColor(color.value)"
                                    :title="color.name"
                                ></button>
                            </div>
                        </div>
                    </div>

                    <!-- Size -->
                    <div class="filter-group">
                        <div class="filter-group-header" @click="toggleGroup('sizes')">
                            <span>SIZE</span>
                            <i :class="openGroups.sizes ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>
                        </div>
                        <div class="filter-group-body" v-show="openGroups.sizes">
                            <div class="size-chips">
                                <button
                                    v-for="size in sampleSizes"
                                    :key="size"
                                    class="size-chip"
                                    :class="{ active: selectedSizes.includes(size) }"
                                    @click="toggleSize(size)"
                                >{{ size }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- Collection -->
                    <div class="filter-group">
                        <div class="filter-group-header" @click="toggleGroup('collections')">
                            <span>COLLECTION</span>
                            <i :class="openGroups.collections ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>
                        </div>
                        <div class="filter-group-body" v-show="openGroups.collections">
                            <div class="collection-chips">
                                <button
                                    v-for="col in sampleCollections"
                                    :key="col.id"
                                    class="collection-chip"
                                    :class="{ active: selectedCollections.includes(col.id) }"
                                    @click="toggleCollection(col.id)"
                                >{{ col.name }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- Garment -->
                    <div class="filter-group">
                        <div class="filter-group-header" @click="toggleGroup('garments')">
                            <span>GARMENT</span>
                            <i :class="openGroups.garments ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>
                        </div>
                        <div class="filter-group-body" v-show="openGroups.garments">
                            <label class="filter-checkbox" v-for="g in sampleGarments" :key="g">
                                <input type="checkbox" :value="g" v-model="selectedGarments">
                                <span>{{ g }}</span>
                            </label>
                            <div class="garment-chips">
                                <button
                                    v-for="g in sampleGarmentChips"
                                    :key="g"
                                    class="garment-chip"
                                >{{ g }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- Product Type -->
                    <div class="filter-group">
                        <div class="filter-group-header" @click="toggleGroup('productTypes')">
                            <span>PRODUCT TYPE</span>
                            <i :class="openGroups.productTypes ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>
                        </div>
                        <div class="filter-group-body" v-show="openGroups.productTypes">
                            <label class="filter-checkbox" v-for="pt in sampleProductTypes" :key="pt">
                                <input type="checkbox" :value="pt" v-model="selectedProductTypes">
                                <span>{{ pt }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="filter-group">
                        <div class="filter-group-header" @click="toggleGroup('price')">
                            <span>PRICE FILTER</span>
                            <i :class="openGroups.price ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>
                        </div>
                        <div class="filter-group-body" v-show="openGroups.price">
                            <div class="price-range-wrap">
                                <input
                                    type="range"
                                    class="price-range"
                                    min="100"
                                    max="5000"
                                    v-model="priceMax"
                                />
                                <div class="price-range-labels">
                                    <span>PHP 100.00 - {{ formatPrice(priceMax) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </aside>

                <!-- Products Grid -->
                <div class="shop-products">
                    <div class="products-grid">
                        <div class="product-card" v-for="product in sampleProducts" :key="product.id">

                            <!-- Badges -->
                            <div class="product-card-badges">
                                <span class="badge-sale" v-if="product.badge === 'sale'">Sale</span>
                                <span class="badge-new" v-if="product.badge === 'new'">New</span>
                            </div>

                            <!-- Wishlist -->
                            <button class="product-wishlist-btn">
                                <i class="ri-heart-line"></i>
                            </button>

                            <!-- Image -->
                            <div class="product-card-image">
                                <div class="product-card-img-placeholder"></div>
                                <!-- Replace with: <img :src="product.image_url" :alt="product.name"> -->
                            </div>

                            <!-- Info -->
                            <div class="product-card-body">
                                <p class="product-card-collection">{{ product.collection }}</p>
                                <h3 class="product-card-name">{{ product.name }}</h3>

                                <!-- Stars -->
                                <div class="product-card-stars">
                                    <i class="ri-star-fill" v-for="n in 5" :key="n" :class="n <= product.rating ? 'star-filled' : 'star-empty'"></i>
                                </div>

                                <!-- Price -->
                                <div class="product-card-price-row">
                                    <span class="product-card-price" :class="{ 'has-sale': product.original_price }">
                                        PHP {{ product.price.toFixed(2) }}
                                    </span>
                                    <span class="product-card-original" v-if="product.original_price">
                                        PHP {{ product.original_price.toFixed(2) }}
                                    </span>
                                    <span class="product-card-saved" v-if="product.saved">
                                        Save {{ product.saved }}
                                    </span>
                                </div>

                                <!-- Add to Cart -->
                                <button class="product-card-atc">ADD TO CART</button>
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
import { ref, reactive } from 'vue';

const showFilters = ref(true);

const openGroups = reactive({
    categories: true,
    colors: true,
    sizes: true,
    collections: true,
    garments: true,
    productTypes: true,
    price: true,
});

const toggleGroup = (group) => {
    openGroups[group] = !openGroups[group];
};

// Selected filters
const selectedCategory = ref(null);
const selectedColors = ref([]);
const selectedSizes = ref([]);
const selectedCollections = ref([]);
const selectedGarments = ref([]);
const selectedProductTypes = ref([]);
const priceMax = ref(5000);

const toggleColor = (val) => {
    const idx = selectedColors.value.indexOf(val);
    if (idx > -1) selectedColors.value.splice(idx, 1);
    else selectedColors.value.push(val);
};

const toggleSize = (val) => {
    const idx = selectedSizes.value.indexOf(val);
    if (idx > -1) selectedSizes.value.splice(idx, 1);
    else selectedSizes.value.push(val);
};

const toggleCollection = (val) => {
    const idx = selectedCollections.value.indexOf(val);
    if (idx > -1) selectedCollections.value.splice(idx, 1);
    else selectedCollections.value.push(val);
};

const formatPrice = (val) => `PHP ${Number(val).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;

// ===== SAMPLE DATA (remove when backend is connected) =====
const sampleCategories = [
    { id: 1, name: 'Caps' },
    { id: 2, name: 'Sportswear' },
    { id: 3, name: 'Bags' },
    { id: 4, name: 'Medals' },
    { id: 5, name: 'Plaques' },
    { id: 6, name: 'Hoodies' },
    { id: 7, name: 'Special Items' },
    { id: 8, name: 'Accessories' },
];

const sampleColors = [
    { name: 'Red', value: 'red', hex: '#e74c3c' },
    { name: 'Orange', value: 'orange', hex: '#e67e22' },
    { name: 'Yellow', value: 'yellow', hex: '#f1c40f' },
    { name: 'Green', value: 'green', hex: '#2ecc71' },
    { name: 'Teal', value: 'teal', hex: '#1abc9c' },
    { name: 'Blue', value: 'blue', hex: '#3498db' },
    { name: 'Purple', value: 'purple', hex: '#9b59b6' },
    { name: 'Pink', value: 'pink', hex: '#e91e8c' },
    { name: 'Gray', value: 'gray', hex: '#bdc3c7' },
    { name: 'Black', value: 'black', hex: '#2c3e50' },
    { name: 'Light Gray', value: 'lightgray', hex: '#dfe6e9' },
];

const sampleSizes = ['XXS', 'XS', 'SMALL', 'MEDIUM', 'LARGE', 'XL', 'XXL'];

const sampleCollections = [
    { id: 1, name: 'The Dreamer' },
    { id: 2, name: 'HUGIS' },
    { id: 3, name: 'Kuris Koleksyon' },
];

const sampleGarments = ['Singlet', 'Tee Shirt', 'Longsleeves', 'Jersey'];
const sampleGarmentChips = ['Singlet', 'Tee Shirt', 'LongSleeves', 'Jersey'];

const sampleProductTypes = ['Running', 'Trail', 'Cycling', 'Casual'];

const sampleProducts = [
    { id: 1, name: 'Product Name Goes Here', collection: 'HUGIS', price: 220.00, original_price: 420.00, saved: null, rating: 5, badge: 'sale' },
    { id: 2, name: 'Product Name Goes Here', collection: 'MOUNTAIN DREAM', price: 220.00, original_price: 420.00, saved: null, rating: 5, badge: null },
    { id: 3, name: 'Product Name Goes Here', collection: 'FUNNY SOCKS', price: 220.00, original_price: null, saved: null, rating: 5, badge: null },
    { id: 4, name: 'Product Name Goes Here', collection: 'THE DREAMER', price: 220.00, original_price: 420.00, saved: '₱20', rating: 4, badge: null },
    { id: 5, name: 'Product Name Goes Here', collection: 'KURIS KOLEKSYON', price: 220.00, original_price: 420.00, saved: null, rating: 5, badge: 'new' },
    { id: 6, name: 'Product Name Goes Here', collection: 'HUGIS V2', price: 220.00, original_price: 420.00, saved: null, rating: 4, badge: null },
    { id: 7, name: 'Product Name Goes Here', collection: 'HUGIS', price: 220.00, original_price: 420.00, saved: null, rating: 5, badge: 'sale' },
    { id: 8, name: 'Product Name Goes Here', collection: 'KURIS KOLEKSYON', price: 220.00, original_price: 420.00, saved: null, rating: 4, badge: 'new' },
    { id: 9, name: 'Product Name Goes Here', collection: 'HUGIS V2', price: 220.00, original_price: 420.00, saved: null, rating: 5, badge: null },
    { id: 10, name: 'Product Name Goes Here', collection: 'FUNNY SOCKS', price: 220.00, original_price: null, saved: null, rating: 5, badge: null },
    { id: 11, name: 'Product Name Goes Here', collection: 'THE DREAMER', price: 220.00, original_price: 420.00, saved: null, rating: 4, badge: null },
    { id: 12, name: 'Product Name Goes Here', collection: 'MOUNTAIN DREAM', price: 225.00, original_price: 420.00, saved: null, rating: 5, badge: null },
];
// ===== END SAMPLE DATA =====
</script>

<style scoped>
.shop-page {
    font-family: 'Public Sans', sans-serif;
    background: #fff;
    min-height: 100vh;
    padding-top: 100px;
}

.shop-inner {
    max-width: 1300px;
    margin: 0 auto;
    padding: 0 32px;
}

.shop-title {
    font-size: 60px;
    font-weight: 800;
    text-align: left;
    padding-top: 15px;
    color: #535353;
    letter-spacing: -1px;
    line-height: 1.2;
    font-family: 'Arial', 'Helvetica', sans-serif;
    display: block;
    width: 100%;
}

/* Toolbar */
.shop-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0 20px;
    border-bottom: 1px solid #eee;
    margin-bottom: 24px;
}

.shop-toolbar-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.toolbar-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    background: none;
    border: none;
    font-size: 13px;
    font-weight: 600;
    color: #333;
    cursor: pointer;
    padding: 6px 10px;
    font-family: 'Public Sans', sans-serif;
    transition: color 0.2s;
}

.toolbar-btn:hover {
    color: #e84b0f;
}

.toolbar-btn i {
    font-size: 16px;
}

.toolbar-featured {
    font-size: 13px;
    color: #888;
    font-weight: 500;
}

/* Layout */
.shop-layout {
    display: grid;
    grid-template-columns: 220px 1fr;
    gap: 40px;
    align-items: start;
    padding-bottom: 80px;
}

/* Sidebar */
.shop-sidebar {
    position: sticky;
    top: 90px;
    display: flex;
    flex-direction: column;
    gap: 0;
}

.filter-group {
    border-bottom: 1px solid #eee;
    padding: 14px 0;
}

.filter-group-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    font-size: 12px;
    font-weight: 700;
    color: #111;
    letter-spacing: 0.8px;
    padding: 4px 0;
    user-select: none;
}

.filter-group-header i {
    font-size: 16px;
    color: #888;
}

.filter-group-body {
    padding-top: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* Radio / Checkbox */
.filter-radio,
.filter-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: #444;
    cursor: pointer;
}

.filter-radio input,
.filter-checkbox input {
    accent-color: #e84b0f;
    width: 15px;
    height: 15px;
    cursor: pointer;
}

/* Color Swatches */
.color-swatches {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.color-swatch {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.2s;
    outline: none;
}

.color-swatch.active,
.color-swatch:hover {
    border-color: #111;
    transform: scale(1.15);
}

/* Size Chips */
.size-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.size-chip {
    padding: 5px 10px;
    border: 1.5px solid #ddd;
    background: #fff;
    font-size: 11px;
    font-weight: 700;
    color: #444;
    cursor: pointer;
    border-radius: 4px;
    font-family: 'Public Sans', sans-serif;
    transition: all 0.2s;
}

.size-chip.active,
.size-chip:hover {
    border-color: #111;
    background: #111;
    color: #fff;
}

/* Collection Chips */
.collection-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.collection-chip {
    padding: 5px 12px;
    border-radius: 20px;
    border: 1.5px solid #ddd;
    background: #fff;
    font-size: 11px;
    font-weight: 600;
    color: #444;
    cursor: pointer;
    font-family: 'Public Sans', sans-serif;
    transition: all 0.2s;
}

.collection-chip.active,
.collection-chip:hover {
    background: #e84b0f;
    border-color: #e84b0f;
    color: #fff;
}

/* Garment Chips */
.garment-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 8px;
}

.garment-chip {
    padding: 5px 12px;
    border-radius: 4px;
    border: 1.5px solid #ddd;
    background: #fff;
    font-size: 11px;
    font-weight: 600;
    color: #444;
    cursor: pointer;
    font-family: 'Public Sans', sans-serif;
    transition: all 0.2s;
}

.garment-chip:hover {
    border-color: #111;
    color: #111;
}

/* Price Range */
.price-range-wrap {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.price-range {
    width: 100%;
    accent-color: #e84b0f;
    cursor: pointer;
}

.price-range-labels {
    font-size: 12px;
    color: #555;
    font-weight: 500;
}

/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

/* Product Card */
.product-card {
    position: relative;
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1px solid #f0f0f0;
    transition: box-shadow 0.25s, transform 0.25s;
}

.product-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,0.1);
    transform: translateY(-3px);
}

/* Badges */
.product-card-badges {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 2;
    display: flex;
    gap: 6px;
}

.badge-sale {
    background: #e84b0f;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 3px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-new {
    background: #1a5c3a;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 3px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Wishlist */
.product-wishlist-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 2;
    background: #fff;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    font-size: 16px;
    color: #888;
    transition: all 0.2s;
}

.product-wishlist-btn:hover {
    color: #e84b0f;
    box-shadow: 0 2px 12px rgba(232,75,15,0.2);
}

/* Image */
.product-card-image {
    width: 100%;
    aspect-ratio: 1 / 1;
    overflow: hidden;
    background: #f5f5f5;
}

.product-card-img-placeholder {
    width: 100%;
    height: 100%;
    background: #e8e8e8;
    min-height: 200px;
}

.product-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.4s ease;
}

.product-card:hover .product-card-image img,
.product-card:hover .product-card-img-placeholder {
    transform: scale(1.04);
}

/* Card Body */
.product-card-body {
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.product-card-collection {
    font-size: 11px;
    font-weight: 600;
    color: #888;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-card-name {
    font-size: 15px;
    font-weight: 700;
    color: #111;
    margin: 0;
    font-family: 'Public Sans', sans-serif;
    line-height: 1.3;
}

/* Stars */
.product-card-stars {
    display: flex;
    gap: 2px;
}

.product-card-stars i {
    font-size: 14px;
}

.star-filled {
    color: #f5a623;
}

.star-empty {
    color: #ddd;
}

/* Price */
.product-card-price-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.product-card-price {
    font-size: 15px;
    font-weight: 800;
    color: #e84b0f;
    font-family: 'Public Sans', sans-serif;
}

.product-card-price.has-sale {
    color: #e84b0f;
}

.product-card-original {
    font-size: 12px;
    color: #aaa;
    text-decoration: line-through;
}

.product-card-saved {
    font-size: 10px;
    font-weight: 700;
    background: #fff3e0;
    color: #e84b0f;
    padding: 2px 7px;
    border-radius: 10px;
}

/* Add to Cart */
.product-card-atc {
    margin-top: 8px;
    width: 100%;
    padding: 11px;
    background: #fff;
    border: 1.5px solid #ddd;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.5px;
    color: #333;
    cursor: pointer;
    font-family: 'Public Sans', sans-serif;
    transition: all 0.25s;
    text-transform: uppercase;
}

.product-card-atc:hover {
    background: #111;
    border-color: #111;
    color: #fff;
}

/* Responsive */
@media (max-width: 991px) {
    .shop-layout {
        grid-template-columns: 1fr;
    }

    .shop-sidebar {
        position: static;
    }

    .products-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 575px) {
    .shop-inner {
        padding: 0 16px;
    }

    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .product-card-name {
        font-size: 13px;
    }
}
</style>