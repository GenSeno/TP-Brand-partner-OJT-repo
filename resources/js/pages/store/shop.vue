    <template>
        <Head title="Our Products" />

        <div class="shop-page">
            <div class="shop-inner">
                <h1 class="shop-title">Our Products</h1>

                <!-- Toolbar -->
                <div class="shop-toolbar">
                    <div class="shop-toolbar-left">
                        <button
                            class="toolbar-btn"
                            @click="showFilters = !showFilters"
                        >
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
                            <div
                                class="filter-group-header"
                                @click="toggleGroup('categories')"
                            >
                                <span>CATEGORIES</span>
                                <i
                                    :class="
                                        openGroups.categories
                                            ? 'ri-arrow-up-s-line'
                                            : 'ri-arrow-down-s-line'
                                    "
                                ></i>
                            </div>
                            <div
                                class="filter-group-body"
                                v-show="openGroups.categories"
                            >
                                <label
                                    class="filter-radio"
                                    v-for="cat in sampleCategories"
                                    :key="cat.id"
                                >
                                    <input
                                        type="radio"
                                        name="category"
                                        :value="cat.id"
                                        v-model="selectedCategory"
                                    />
                                    <span>{{ cat.name }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Color -->
                        <div class="filter-group">
                            <div
                                class="filter-group-header"
                                @click="toggleGroup('colors')"
                            >
                                <span>COLOR</span>
                                <i
                                    :class="
                                        openGroups.colors
                                            ? 'ri-arrow-up-s-line'
                                            : 'ri-arrow-down-s-line'
                                    "
                                ></i>
                            </div>
                            <div
                                class="filter-group-body"
                                v-show="openGroups.colors"
                            >
                                <div class="color-swatches">
                                    <button
                                        v-for="color in sampleColors"
                                        :key="color.value"
                                        class="color-swatch"
                                        :style="{ background: color.hex }"
                                        :class="{
                                            active: selectedColors.includes(
                                                color.value,
                                            ),
                                        }"
                                        @click="toggleColor(color.value)"
                                        :title="color.name"
                                    ></button>
                                </div>
                            </div>
                        </div>

                        <!-- Size -->
                        <div class="filter-group">
                            <div
                                class="filter-group-header"
                                @click="toggleGroup('sizes')"
                            >
                                <span>SIZE</span>
                                <i
                                    :class="
                                        openGroups.sizes
                                            ? 'ri-arrow-up-s-line'
                                            : 'ri-arrow-down-s-line'
                                    "
                                ></i>
                            </div>
                            <div
                                class="filter-group-body"
                                v-show="openGroups.sizes"
                            >
                                <div class="size-chips">
                                    <button
                                        v-for="size in sampleSizes"
                                        :key="size"
                                        class="size-chip"
                                        :class="{
                                            active: selectedSizes.includes(size),
                                        }"
                                        @click="toggleSize(size)"
                                    >
                                        {{ size }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Collection -->
                        <div class="filter-group">
                            <div
                                class="filter-group-header"
                                @click="toggleGroup('collections')"
                            >
                                <span>COLLECTION</span>
                                <i
                                    :class="
                                        openGroups.collections
                                            ? 'ri-arrow-up-s-line'
                                            : 'ri-arrow-down-s-line'
                                    "
                                ></i>
                            </div>
                            <div
                                class="filter-group-body"
                                v-show="openGroups.collections"
                            >
                                <div class="collection-chips">
                                    <button
                                        v-for="col in sampleCollections"
                                        :key="col.id"
                                        class="collection-chip"
                                        :class="{
                                            active: selectedCollections.includes(
                                                col.id,
                                            ),
                                        }"
                                        @click="toggleCollection(col.id)"
                                    >
                                        {{ col.name }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Garment -->
                        <div class="filter-group">
                            <div
                                class="filter-group-header"
                                @click="toggleGroup('garments')"
                            >
                                <span>GARMENT</span>
                                <i
                                    :class="
                                        openGroups.garments
                                            ? 'ri-arrow-up-s-line'
                                            : 'ri-arrow-down-s-line'
                                    "
                                ></i>
                            </div>
                            <div
                                class="filter-group-body"
                                v-show="openGroups.garments"
                            >
                                <label
                                    class="filter-checkbox"
                                    v-for="g in sampleGarments"
                                    :key="g"
                                >
                                    <input
                                        type="checkbox"
                                        :value="g"
                                        v-model="selectedGarments"
                                    />
                                    <span>{{ g }}</span>
                                </label>
                                <div class="garment-chips">
                                    <button
                                        v-for="g in sampleGarmentChips"
                                        :key="g"
                                        class="garment-chip"
                                    >
                                        {{ g }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Product Type -->
                        <div class="filter-group">
                            <div
                                class="filter-group-header"
                                @click="toggleGroup('productTypes')"
                            >
                                <span>PRODUCT TYPE</span>
                                <i
                                    :class="
                                        openGroups.productTypes
                                            ? 'ri-arrow-up-s-line'
                                            : 'ri-arrow-down-s-line'
                                    "
                                ></i>
                            </div>
                            <div
                                class="filter-group-body"
                                v-show="openGroups.productTypes"
                            >
                                <label
                                    class="filter-checkbox"
                                    v-for="pt in sampleProductTypes"
                                    :key="pt"
                                >
                                    <input
                                        type="checkbox"
                                        :value="pt"
                                        v-model="selectedProductTypes"
                                    />
                                    <span>{{ pt }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price Filter -->
                        <div class="filter-group">
                            <div
                                class="filter-group-header"
                                @click="toggleGroup('price')"
                            >
                                <span>PRICE FILTER</span>
                                <i
                                    :class="
                                        openGroups.price
                                            ? 'ri-arrow-up-s-line'
                                            : 'ri-arrow-down-s-line'
                                    "
                                ></i>
                            </div>
                            <div
                                class="filter-group-body"
                                v-show="openGroups.price"
                            >
                                <div class="price-range-wrap">
                                    <input
                                        type="range"
                                        class="price-range"
                                        min="50"
                                        max="5000"
                                        v-model="priceMax"
                                    />
                                    <div class="price-range-labels">
                                        <span>PHP 00.00 - PHP {{ priceMax }}.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <!-- Products Grid -->
                    <div class="shop-products">
                        <div class="products-grid" v-if="products.data.length > 0">
                            <div
                                class="product-card"
                                v-for="product in products.data"
                                :key="product.id"
                            >
                                <!-- Badges -->
                                <div class="product-card-badges">
                                    <span
                                        class="badge-sale"
                                        v-if="
                                            product.compare_price &&
                                            product.compare_price > product.price
                                        "
                                        >Sale</span
                                    >
                                </div>

                                <!-- Wishlist -->
                                <button class="product-wishlist-btn">
                                    <i class="ri-heart-line"></i>
                                </button>

                                <!-- Image -->
                                <div class="product-card-image">
                                    <Link
                                        :href="
                                            route(
                                                'store.brand-partner.product',
                                                product.slug,
                                            )
                                        "
                                    >
                                        <img
                                            :src="
                                                product.image_url ||
                                                '/img/tshirt-placeholder.svg'
                                            "
                                            :alt="product.name"
                                        />
                                    </Link>
                                </div>

                                <!-- Info -->
                                <div class="product-card-body">
                                    <p
                                        class="product-card-collection text-uppercase"
                                    >
                                        {{
                                            product.short_description
                                                ? product.short_description.substring(
                                                    0,
                                                    30,
                                                )
                                                : 'COLLECTION'
                                        }}
                                    </p>
                                    <Link
                                        :href="
                                            route(
                                                'store.brand-partner.product',
                                                product.slug,
                                            )
                                        "
                                        style="
                                            text-decoration: none;
                                            color: inherit;
                                        "
                                    >
                                        <h3 class="product-card-name">
                                            {{
                                                product.name ||
                                                'Product Name Goes Here'
                                            }}
                                        </h3>
                                    </Link>

                                    <!-- Stars -->
                                    <div class="product-card-stars">
                                        <i
                                            class="ri-star-fill star-filled"
                                            v-for="n in 5"
                                            :key="n"
                                        ></i>
                                    </div>

                                    <!-- Price -->
                                    <div class="product-card-price-row">
                                        <span
                                            class="product-card-price"
                                            :class="{
                                                'has-sale':
                                                    product.compare_price &&
                                                    product.compare_price >
                                                        product.price,
                                            }"
                                        >
                                            PHP
                                            {{ (product.price / 100).toFixed(2) }}
                                        </span>
                                        <span
                                            class="product-card-original"
                                            v-if="
                                                product.compare_price &&
                                                product.compare_price >
                                                    product.price
                                            "
                                        >
                                            PHP
                                            {{
                                                (product.compare_price / 100).toFixed(2)
                                            }}
                                        </span>
                                    </div>

                                    <!-- Add to Cart -->
                                    <button
                                        class="product-card-atc"
                                        :disabled="!product.in_stock"
                                        @click.prevent="addToCart(product)"
                                    >
                                        ADD TO CART
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Empty State if no products -->
                        <div v-else class="text-center py-5">
                            <i
                                class="ri-shopping-bag-line"
                                style="font-size: 40px; color: #ccc"
                            ></i>
                            <h4 class="mt-3">No products available.</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="modal fade"
            id="addToCartModal"
            tabindex="-1"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered modal-sm-fullwidth">
                <div
                    class="modal-content grocery-modal-content"
                    v-if="selectedProduct"
                >
                    <div class="grocery-modal-header">
                        <h4 class="grocery-modal-title">
                            {{ selectedProduct.name }}
                        </h4>
                        <button
                            type="button"
                            class="btn-close"
                            @click="cartModal?.hide()"
                        ></button>
                    </div>
                    <div class="grocery-modal-body">
                        <div class="modal-product-detail">
                            <img
                                :src="selectedProduct.image_url || '/img/tshirt-placeholder.svg'"
                                :alt="selectedProduct.name"
                                class="modal-product-img"
                            />
                            <div class="modal-product-info">
                                <p v-if="selectedProduct.short_description">
                                    {{ selectedProduct.short_description }}
                                </p>
                                <h5 class="modal-product-price">
                                    {{ formatPrice(selectedProduct.price) }}
                                </h5>
                                <span
                                    v-if="
                                        selectedProduct.compare_price &&
                                        selectedProduct.compare_price >
                                            selectedProduct.price
                                    "
                                    class="old-price"
                                >
                                    {{ formatPrice(selectedProduct.compare_price) }}
                                </span>
                            </div>
                        </div>

                        <div class="qty-section-title">
                            <h5>Quantity</h5>
                        </div>
                        <div class="qty-selector">
                            <div class="qty-box">
                                <div class="input-group">
                                    <button
                                        type="button"
                                        class="qty-btn qty-minus"
                                        @click="modalQuantity > 1 && modalQuantity--"
                                        :disabled="modalQuantity <= 1"
                                    >
                                        -
                                    </button>
                                    <input
                                        class="form-control qty-input"
                                        type="text"
                                        v-model.number="modalQuantity"
                                        min="1"
                                    />
                                    <button
                                        type="button"
                                        class="qty-btn qty-plus"
                                        @click="modalQuantity++"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grocery-modal-footer">
                        <div class="modal-footer-info">
                            <h5>
                                {{ modalQuantity }}
                                {{ modalQuantity === 1 ? 'item' : 'items' }}
                            </h5>
                            <h4>
                                {{ formatPrice(selectedProduct.price * modalQuantity) }}
                            </h4>
                        </div>
                        <button
                            class="btn btn-grocery-primary cart-bar-btn"
                            @click="confirmAddToCart"
                            :disabled="isAddingToCart"
                        >
                            <span v-if="isAddingToCart">Adding...</span>
                            <span v-else>
                                Add to Cart
                                <i class="ri-arrow-right-line"></i>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script setup>
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref, reactive, onMounted, onBeforeUnmount } from 'vue';
    import { Modal } from 'bootstrap';

    const props = defineProps({
        brandPartner: Object,
        categories: Array,
        events: Array,
        products: Object,
        filter: Object,
        cartCount: Number,
    });

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
    const selectedProduct = ref(null);
    const modalQuantity = ref(1);
    const isAddingToCart = ref(false);
    let cartModal = null;
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

    const formatPrice = (val) =>`PHP ${(val / 100).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

    onMounted(() => {
        const modalEl = document.getElementById('addToCartModal');
        if (modalEl) {
            cartModal = new Modal(modalEl);
            modalEl.addEventListener('hidden.bs.modal', () => {
                selectedProduct.value = null;
                modalQuantity.value = 1;
                isAddingToCart.value = false;
            });
        }
    });

    onBeforeUnmount(() => {
        if (cartModal) {
            cartModal.dispose();
            cartModal = null;
        }
    });

    const addToCart = (product) => {
        selectedProduct.value = product;
        modalQuantity.value = 1;
        cartModal?.show();
    };

    const confirmAddToCart = () => {
        if (!selectedProduct.value) return;

        isAddingToCart.value = true;
        router.post(
            route('store.brand-partner.cart.add'),
            {
                product_id: selectedProduct.value.id,
                quantity: modalQuantity.value,
                color: selectedProduct.value.colors_array?.[0] || null,
                size: selectedProduct.value.sizes_array?.[0] || null,
            },
            {
                preserveScroll: true,
                onSuccess: () => cartModal?.hide(),
                onFinish: () => {
                    isAddingToCart.value = false;
                },
            },
        );
    };
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
        transition:
            box-shadow 0.25s,
            transform 0.25s;
    }

    .product-card:hover {
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.1);
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        font-size: 16px;
        color: #888;
        transition: all 0.2s;
    }

    .product-wishlist-btn:hover {
        color: #e84b0f;
        box-shadow: 0 2px 12px rgba(232, 75, 15, 0.2);
    }

    /* Image */
    .product-card-image {
        position: relative;
        width: 100%;
        overflow: hidden;
        aspect-ratio: 4 / 3;
        min-height: 220px;
    }

    .product-card-img-placeholder {
        width: 100%;
        height: 100%;
        background: #e8e8e8;
    }

    .product-card-image img {
        position: absolute;
        top: 0;
        left: 0;
        transition: transform 0.4s ease;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-card:hover .product-card-image img,
    .product-card:hover .product-card-img-placeholder {
        transform: scale(1.02);
    }

    /* Card Body */
    .product-card-body {
        padding: 14px 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-height: 200px;
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
        margin-top: auto;
        width: 100%;
        padding: 10px;
        background: #fff;
        border: 1.5px solid #198754;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.25px;
        color: #198754;
        cursor: pointer;
        font-family: 'Public Sans', sans-serif;
        transition: all 0.25s;
        text-transform: uppercase;
    }

    .product-card-atc:hover {
        background: #ff9505;
        border: 1px solid #ff9505;
        color: #fff;
    }

        .grocery-modal,
    .grocery-modal-content {
        border: none;
        border-radius: 22px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.12);
        font-family: 'Public Sans', sans-serif;
    }

    .grocery-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        background: #fff;
        border-bottom: 1px solid #f1f1f1;
    }

    .grocery-modal-title {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #111;
    }

    .grocery-modal-body {
        padding: 20px;
        background: #fff;
    }

    .modal-product-detail {
        display: flex;
        gap: 16px;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .modal-product-img {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 18px;
        border: 1px solid #f0f0f0;
        background: #f8f8f8;
    }

    .modal-product-info p {
        margin: 0 0 10px;
        color: #5a5a5a;
        font-size: 13px;
        line-height: 1.5;
    }

    .modal-product-price {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #e84b0f;
    }

    .old-price {
        display: block;
        margin-top: 8px;
        font-size: 13px;
        color: #999;
    }

    .qty-section-title {
        margin-bottom: 10px;
        font-size: 14px;
        font-weight: 700;
        color: #111;
    }

    .qty-selector {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
    }

    .qty-box {
        display: inline-flex;
        align-items: center;
        background: #f7f7f7;
        border-radius: 18px;
        border: 1px solid #e4e4e4;
        overflow: hidden;
    }

    .input-group {
        display: inline-flex;
        align-items: center;
    }

    .qty-btn {
        width: 42px;
        height: 42px;
        border: none;
        background: #fff;
        color: #111;
        font-size: 18px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s, color 0.2s;
    }

    .qty-btn:hover:not(:disabled) {
        background: #f0f0f0;
    }

    .qty-btn:disabled {
        color: #ccc;
        cursor: not-allowed;
    }

    .qty-input {
        width: 68px;
        border: none;
        text-align: center;
        background: transparent;
        font-size: 15px;
        font-weight: 700;
        color: #111;
        padding: 0 12px;
        outline: none;
    }

    .grocery-modal-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 18px 22px;
        background: #fafafa;
        border-top: 1px solid #f1f1f1;
    }

    .modal-footer-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .modal-footer-info h5,
    .modal-footer-info h4 {
        margin: 0;
        color: #111;
    }

    .modal-footer-info h4 {
        font-size: 20px;
        font-weight: 900;
    }

    .cart-bar-btn {
        min-width: 150px;
        padding: 12px 16px;
        border-radius: 16px;
        border: none;
        background: #e84b0f;
        color: #fff;
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        cursor: pointer;
    }

    .cart-bar-btn:hover:not(:disabled) {
        background: #d96f0d;
    }

    .cart-bar-btn:disabled {
        opacity: 0.75;
        cursor: not-allowed;
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
