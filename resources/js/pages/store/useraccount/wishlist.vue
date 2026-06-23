<template>
  <Head :title="`Wishlist - ${brandPartner.name}`" />

  <div class="wishlist-section">
    <div class="wishlist-container">

      <!-- Page Header -->
      <div class="page-header">
        <h1 class="page-title">My Wishlist</h1>
        <p class="page-subtitle">Items you've saved for later</p>
      </div>

      <!-- Items Grid -->
        <div v-if="visibleItems.length > 0" class="products-grid">
        <div
          class="product-card"
          v-for="item in visibleItems"
          :key="item.id"
          @mouseenter="hoverMap[item.product.id] = true"
          @mouseleave="hoverMap[item.product.id] = false"
        >
          <!-- SALE Badge -->
          <div class="product-card-badges">
            <span
              class="badge-sale"
              v-if="item.product.compare_price && item.product.compare_price > item.product.price"
            >Sale</span>
          </div>

          <!-- Remove (heart filled = wishlisted, click to remove) -->
          <button
            class="product-wishlist-btn"
            @click="removeItem(item.id)"
            title="Remove from wishlist"
          >
            <i class="ri-heart-fill"></i>
          </button>

          <!-- Image -->
          <div class="product-card-image">
            <Link :href="route('store.brand-partner.product', item.product.slug)">
              <img
                :src="
                  hoverMap[item.product.id] &&
                  item.product.images &&
                  item.product.images.length > 1
                    ? item.product.images[1]?.url || item.product.image_url
                    : item.product.image_url || '/img/tshirt-placeholder.svg'
                "
                :alt="item.product.name"
              />
            </Link>
          </div>

          <!-- Card Body -->
          <div class="product-card-body">
            <Link
              :href="route('store.brand-partner.product', item.product.slug)"
              style="text-decoration: none; color: inherit"
            >
              <h3 class="product-card-name">
                {{ item.product.name || 'Product Name Goes Here' }}
              </h3>
            </Link>

            <p class="product-card-collection">
              {{
                item.product.category?.name ||
                item.product.collection?.label ||
                (item.product.short_description
                  ? item.product.short_description.substring(0, 30)
                  : brandPartner.name)
              }}
            </p>

            <!-- Stars -->
            <div class="product-card-stars">
              <i class="ri-star-fill star-filled" v-for="n in 5" :key="n"></i>
            </div>

            <!-- Price -->
            <div class="product-card-price-row">
              <span
                class="product-card-price"
                :class="{
                  'has-sale':
                    item.product.compare_price &&
                    item.product.compare_price > item.product.price,
                }"
              >
                PHP {{ (item.product.price / 100).toFixed(2) }}
              </span>
              <span
                class="product-card-original"
                v-if="item.product.compare_price && item.product.compare_price > item.product.price"
              >
                PHP {{ (item.product.compare_price / 100).toFixed(2) }}
              </span>
            </div>

            <!-- Add to Cart -->
            <button
              class="product-card-atc"
              :disabled="addingToCart === item.product.id"
              @click.prevent="addToCart(item.product)"
            >
              <span v-if="addingToCart === item.product.id">Adding...</span>
              <span v-else-if="!item.product.in_stock">PRE-ORDER</span>
              <span v-else>ADD TO CART</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-wishlist">
        <div class="empty-icon">
          <i class="ri-heart-line"></i>
        </div>
        <h3>Your wishlist is empty</h3>
        <p>Save your favorite items here and come back to them anytime.</p>
        <Link
          :href="route('store.brand-partner.shop', brandPartner.slug)"
          class="shop-btn"
        >
          <i class="ri-store-2-line"></i> Start Shopping
        </Link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
  brandPartner: Object,
  items: Array,
});

const addingToCart = ref(null);
const hoverMap = ref({});
const localItems = ref([...props.items]);

const visibleItems = computed(() => localItems.value.filter(i => i.product));

const removeItem = (itemId) => {
  const idx = localItems.value.findIndex(i => i.id === itemId);
  if (idx === -1) return;
  const removed = localItems.value.splice(idx, 1);

  fetch(route('store.brand-partner.wishlist.remove', itemId), {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      'X-Requested-With': 'XMLHttpRequest',
    },
  }).then(r => {
    if (!r.ok) throw new Error();
  }).catch(() => {
    localItems.value.splice(idx, 0, removed[0]);
  });
};

const addToCart = (product) => {
  addingToCart.value = product.id;
  router.post(
    route('store.brand-partner.cart.add'),
    { product_id: product.id, quantity: 1 },
    {
      preserveScroll: true,
      onFinish: () => {
        addingToCart.value = null;
      },
    },
  );
};
</script>

<style scoped>
/* ── Animation Variables ─────────────────────────────── */
:root {
  --animation-timing-unit: 80ms;
  --animation-timing-300: calc(var(--animation-timing-unit) * 3);
  --ease-out-quart: cubic-bezier(0.165, 0.84, 0.44, 1);
}

/* ── Layout ──────────────────────────────────────────── */
.wishlist-section {
  background: #fff;
  min-height: 60vh;
  padding-bottom: 80px;
  font-family: 'Public Sans', sans-serif;
}

.wishlist-container {
  max-width: 1300px;
  margin: 0 auto;
  padding: 32px 32px 0;
}

/* ── Page Header ─────────────────────────────────────── */
.page-header {
  margin-bottom: 28px;
}

.page-title {
  font-size: 22px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px;
}

.page-subtitle {
  font-size: 13px;
  color: #888;
  margin: 0;
}

/* ── Products Grid (matches shop) ────────────────────── */
.products-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

/* ── Product Card (identical to shop) ───────────────── */
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

/* ── Badges ──────────────────────────────────────────── */
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

/* ── Wishlist / Remove Button ────────────────────────── */
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
  color: #e84b0f;
  transition: all 0.2s;
}

.product-wishlist-btn:hover {
  box-shadow: 0 2px 12px rgba(232, 75, 15, 0.25);
  color: #c0392b;
}

/* ── Product Image ───────────────────────────────────── */
.product-card-image {
  position: relative;
  width: 100%;
  overflow: hidden;
  aspect-ratio: 3 / 3;
  min-height: 250px;
}

.product-card-image img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition-duration: var(--animation-timing-300);
  transition-timing-function: var(--ease-out-quart);
  transition-property: opacity, transform;
}

.product-card:hover .product-card-image img {
  transform: scale(1.02);
}

/* ── Card Body ───────────────────────────────────────── */
.product-card-body {
  padding: 14px 12px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-height: 180px;
}

.product-card-name {
  font-size: 15px;
  font-weight: 700;
  color: #111;
  margin: 0;
  font-family: 'Public Sans', sans-serif;
  line-height: 1.3;
}

.product-card-collection {
  font-size: 11px;
  font-weight: 600;
  color: #888;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* ── Stars ───────────────────────────────────────────── */
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

/* ── Price ───────────────────────────────────────────── */
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

/* ── Add to Cart Button ──────────────────────────────── */
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

.product-card-atc:hover:not(:disabled) {
  background: #ff9505;
  border-color: #ff9505;
  color: #fff;
}

.product-card-atc:disabled {
  border-color: #ccc;
  color: #ccc;
  cursor: not-allowed;
}

/* ── Empty State ─────────────────────────────────────── */
.empty-wishlist {
  text-align: center;
  padding: 100px 20px;
}

.empty-icon {
  width: 100px;
  height: 100px;
  background: #fff5e8;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

.empty-icon i {
  font-size: 44px;
  color: #ff9505;
}

.empty-wishlist h3 {
  font-size: 20px;
  font-weight: 800;
  color: #1a1a1a;
  margin: 0 0 8px;
}

.empty-wishlist p {
  font-size: 14px;
  color: #888;
  margin: 0 0 24px;
}

.shop-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 11px 28px;
  background: #ff9505;
  color: #fff;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  letter-spacing: 0.4px;
  transition: background 0.2s;
}

.shop-btn:hover {
  background: #1b5e38;
}

/* ── Responsive ──────────────────────────────────────── */
@media (max-width: 991px) {
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 575px) {
  .wishlist-container {
    padding: 24px 16px 0;
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