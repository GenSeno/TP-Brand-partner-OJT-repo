<template>
  <Head :title="brandPartner.name" />

  <div class="grocery-store-page">
    <ToastComponent />
    <!-- Hero Carousel Section -->
    <section class="hero-carousel-section">
      <div
        id="heroCarousel"
        class="carousel slide hero-carousel"
        data-bs-ride="carousel"
        data-bs-interval="4000"
        data-bs-pause="false"
      >
        <div class="carousel-indicators">
          <button
            type="button"
            data-bs-target="#heroCarousel"
            data-bs-slide-to="0"
            class="active"
            aria-current="true"
            aria-label="Slide 1"
          ></button>
          <button
            type="button"
            data-bs-target="#heroCarousel"
            data-bs-slide-to="1"
            aria-label="Slide 2"
          ></button>
          <button
            type="button"
            data-bs-target="#heroCarousel"
            data-bs-slide-to="2"
            aria-label="Slide 3"
          ></button>
          <button
            type="button"
            data-bs-target="#heroCarousel"
            data-bs-slide-to="3"
            aria-label="Slide 4"
          ></button>
        </div>
        <div class="carousel-inner">
          <button
            class="carousel-control-prev custom-arrow"
            type="button"
            data-bs-target="#heroCarousel"
            data-bs-slide="prev"
          >
            <span class="arrow-icon">&#10094;</span>
          </button>

          <!-- Next Button -->
          <button
            class="carousel-control-next custom-arrow"
            type="button"
            data-bs-target="#heroCarousel"
            data-bs-slide="next"
          >
            <span class="arrow-icon">&#10095;</span>
          </button>
          <!-- Slide 1: Collection (img-carousel2) [WAS SLIDE 2] -->
          <div class="carousel-item active slide-2-bg">
            <div class="slide-layout">
              <div class="slide-content-left px-5">
                <h1 class="slide-title text-white">HUGIS COLLECTION V2</h1>
                <p class="slide-subtitle text-white">
                  HUGIS COLLECTION V2 celebrates this collective energy. It
                  honors individuality while embracing the beauty of community,
                  proving that when runners move as one, their diversity becomes
                  the masterpiece.
                </p>
                <a
                  :href="route('store.brand-partner.shop', brandPartner?.slug)"
                  class="btn slide-btn-outline"
                  >VIEW ALL PRODUCTS</a
                >
              </div>
            </div>
          </div>

          <!-- Slide 2: Keep On Breaking Boundaries (img-carousel1) [WAS SLIDE 1] -->
          <div class="carousel-item slide-1-bg">
            <div class="slide-overlay-left-dark"></div>
            <div class="slide-layout">
              <div class="slide-content-left px-5" style="z-index: 2">
                <h1 class="slide-title text-white">
                  KEEP ON<br />BREAKING THE<br />BOUNDARIES.
                </h1>
                <p class="slide-subtitle text-white mt-3">
                  Tribu Pakaras is launching its official eCommerce platform
                  soon, powered by upgraded production, improved quality, and
                  expanded product offerings designed for athletes who demand
                  more.
                </p>
                <a
                  :href="route('store.brand-partner.shop', brandPartner.slug)"
                  class="btn slide-btn-outline mt-4"
                  >VIEW ALL PRODUCTS</a
                >
              </div>
            </div>
          </div>

          <!-- Slide 3: Dare To Dream Big (img-carousel3) -->
          <div class="carousel-item slide-3-bg">
            <div class="slide-overlay-left-orange"></div>
            <div class="slide-layout">
              <div class="slide-content-left px-5" style="z-index: 2">
                <h1 class="slide-title text-white">
                  DARE TO DREAM BIG — KEEP ON BREAKING THE BOUNDARIES.
                </h1>
                <p class="slide-subtitle text-white mt-3">
                  Tribu Pakaras is launching its official eCommerce platform
                  soon, powered by upgraded production, improved quality, and
                  expanded product offerings designed for athletes who demand
                  more.
                </p>
                <Link
                  :href="route('store.brand-partner.shop', brandPartner?.slug)"
                  class="btn slide-btn-outline mt-4"
                  >VIEW ALL PRODUCTS</Link
                >
              </div>
            </div>
          </div>

          <!-- Slide 4: Believe In Your Dreams (img-carousel4) -->
          <div class="carousel-item slide-4-bg">
            <div
              class="slide-layout justify-content-center w-100 text-center flex-column"
            ></div>
          </div>
        </div>
      </div>
    </section>

    <!-- 
        =========================================
        EVENT TABS - Grocery Events Navigation
        =========================================
        <section class="grocery-events-section" v-if="events.length > 0">
            <div class="events-tabs-container">
                <div class="scroll-fade-left"></div>
                <ul class="nav nav-pills tab-style-5" role="tablist">
                    <li class="nav-item" v-for="event in events" :key="event.id" role="presentation">
                        <button
                            class="nav-link"
                            :class="{ active: selectedEvent == event.id, loading: event.loading }"
                            type="button"
                            role="tab"
                            :aria-selected="selectedEvent == event.id"
                            :aria-label="`Select ${event.name} event`"
                            @click="selectEvent(event)"
                        >
                            <span class="tab-label">{{ event.name }}</span>
                            <span v-if="event.count" class="event-count-badge">{{ event.count }}</span>
                        </button>
                    </li>
                </ul>
                <div class="scroll-fade-right"></div>
            </div>
        </section>
        -->

    <!-- Category Section -->
    <section
      id="categories"
      class="grocery-category-section"
      v-if="categories.length > 0"
    >
      <div>
        <div class="grocery-category-slider">
          <div class="category-scroll-wrap">
            <a
              href="javascript:void(0)"
              class="grocery-category-box"
              :class="{
                active: !selectedCategory && !selectedEvent,
              }"
              @click="clearFilters"
            >
              <div class="category-icon-wrap">
                <i class="ri-apps-line"></i>
              </div>
              <h5>All</h5>
            </a>
            <a
              v-for="category in categories"
              :key="category.id"
              href="javascript:void(0)"
              class="grocery-category-box"
              :class="{ active: selectedCategory == category.id }"
              @click="selectCategory(category)"
            >
              <div class="category-icon-wrap">
                <i class="ri-price-tag-3-line"></i>
              </div>
              <h5>{{ category.name }}</h5>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Products Grid -->
    <section class="grocery-products-section">
      <div>
        <div class="section-header">
          <h2 class="section-title-main">Our Products</h2>
        </div>

        <ul class="product-offer-list" v-if="products.data.length > 0">
          <li
            v-for="product in products.data"
            :key="product.id"
            class="product-offer-item"
            @mouseenter="hoverImageIndex[product.id] = true"
            @mouseleave="hoverImageIndex[product.id] = false"
          >
            <div class="product-box">
              <div class="product-image-wrap">
                <div class="product-badges">
                  <span
                    class="badge sale-badge"
                    v-if="
                      product.compare_price &&
                      product.compare_price > product.price
                    "
                    >SALE</span
                  >
                  <span
                    class="badge new-badge"
                    v-else-if="isNewProduct(product.created_at)"
                    >NEW
                  </span>
                </div>
                <button
                  @click="toggleWishlist(product.id)"
                  class="wishlist-btn"
                >
                  <i
                    :class="
                      wishlistIds.includes(product.id)
                        ? 'ri-heart-fill text-red-500'
                        : 'ri-heart-line'
                    "
                  ></i>
                </button>
                <Link
                  :href="route('store.brand-partner.product', product.slug)"
                  class="product-image-link"
                >
                  <img
                    :src="
                      hoverImageIndex[product.id] &&
                      product.images &&
                      product.images.length > 1
                        ? product.images[1]?.url || product.image_url
                        : product.image_url || '/img/tshirt-placeholder.svg'
                    "
                    :alt="product.name"
                    class="img-fluid"
                  />
                </Link>
              </div>
              <div class="product-content">
                <Link
                  :href="route('store.brand-partner.product', product.slug)"
                  class="product-name-link"
                >
                  <h5 class="product-name">
                    {{ product.name }}
                  </h5>
                </Link>

                <p
                  class="product-collection text-muted text-uppercase mb-2"
                  style="
                    font-size: 10px;
                    font-weight: 700;
                    letter-spacing: 0.5px;
                  "
                >
                  <Link
                    v-if="product.collection"
                    :href="
                      route('store.brand-partner.shop', {
                        collection: product.collection.id,
                      })
                    "
                    class="text-muted text-decoration-none"
                  >
                    {{ product.collection.label }}
                  </Link>
                </p>

                <p
                  class="product-subtitle text-muted text-uppercase mb-2"
                  style="
                    font-size: 10px;
                    font-weight: 700;
                    letter-spacing: 0.5px;
                  "
                >
                  {{
                    product.short_description
                      ? truncate(product.short_description, 30)
                      : ' '
                  }}
                </p>

                <div class="product-rating mb-2">
                  <i class="ri-star-fill text-warning"></i>
                  <i class="ri-star-fill text-warning"></i>
                  <i class="ri-star-fill text-warning"></i>
                  <i class="ri-star-fill text-warning"></i>
                  <i class="ri-star-fill text-warning"></i>
                </div>

                <div
                  class="product-price-row mb-3 d-flex align-items-center gap-2"
                >
                  <span
                    class="current-price"
                    style="font-size: 12px; font-weight: 700; color: #111"
                  >
                    {{ formatCurrency(product.price) }}
                  </span>
                  <span
                    v-if="
                      product.compare_price &&
                      product.compare_price > product.price
                    "
                    class="old-price text-muted text-decoration-line-through"
                    style="font-size: 11px; margin-left: 4px"
                  >
                    {{ formatCurrency(product.compare_price) }}
                  </span>
                </div>

                <button
                  class="btn add-to-cart-outline-btn w-100"
                  @click.prevent="addToCart(product)"
                  :disabled="!product.in_stock"
                >
                  {{ product.in_stock ? 'ADD TO CART' : 'OUT OF STOCK' }}
                </button>
              </div>
            </div>
          </li>
        </ul>

        <!-- Empty State -->
        <div v-else class="grocery-empty-state">
          <div class="empty-icon-circle">
            <i class="ri-shopping-bag-line"></i>
          </div>
          <h4>No products found</h4>
          <p>Try adjusting your filters or search query.</p>
          <a
            class="btn btn-grocery-primary"
            :href="route('store.brand-partner.shop', brandPartner?.slug)"
          >
            <i class="ri-store-2-line"></i> View All Products
          </a>
        </div>

        <!-- View All Button -->
        <div class="view-all-wrapper" v-if="products.data.length > 0">
          <Link
            :href="route('store.brand-partner.shop', brandPartner.slug)"
            class="view-all-btn"
          >
            VIEW ALL PRODUCTS
          </Link>
        </div>

        <!-- Pagination -->
        <div
          class="grocery-pagination"
          v-if="products.links && products.links.length > 3"
        >
          <nav>
            <ul class="pagination">
              <li
                v-for="link in products.links"
                :key="link.label"
                class="page-item"
                :class="{
                  active: link.active,
                  disabled: !link.url,
                }"
              >
                <Link
                  v-if="link.url"
                  :href="link.url"
                  class="page-link"
                  v-html="link.label"
                  preserve-scroll
                />
                <span v-else class="page-link" v-html="link.label" />
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </section>

    <!-- Check Our Collections Section -->
    <section class="collections-section">
      <!-- THE Collection Banner -->
      <div
        class="dreamer-banner"
        style="
          background-image: url('/img/img-dreamercollection.png');
          background-size: cover;
          background-position: center;
          background-repeat: no-repeat;
        "
      >
        <div class="dreamer-content">
          <span class="dreamer-label">THE</span>
          <h2 class="dreamer-title">DREAMER</h2>
          <p class="dreamer-description">
            <strong>Dare to Dream Big</strong> with our first shirt collection
            for 2026 — <strong>The Dreamer</strong> — featuring the blend of
            milky way &amp; outer space patterns, vectors of limitless
            adventures and shades of greens and cloud dancer which represent the
            colors of 2026.
          </p>
          <a
            :href="
              brandPartner
                ? route('store.brand-partner.collections', brandPartner.slug)
                : '#'
            "
            class="dreamer-btn"
            >CHECK OUR COLLECTIONS</a
          >
        </div>
      </div>

      <!-- 3-Panel Grid -->
      <div class="collections-panels">
        <!-- Panel 1: HUGIS -->
        <div
          class="col-panel panel-dark"
          style="background-image: url('/img/img-indexcollection1.png')"
        >
          <div class="col-panel-overlay"></div>
          <div class="col-panel-body">
            <h3 class="col-panel-title">HUGIS Collection v2</h3>
            <p class="col-panel-desc">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
              eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut
              enim ad minim veniam.
            </p>
            <a
              :href="
                brandPartner
                  ? route('store.brand-partner.collections', brandPartner.slug)
                  : '#'
              "
              class="col-panel-btn col-panel-btn-outline"
              >VIEW COLLECTION</a
            >
          </div>
        </div>

        <!-- Panel 2: Kuris Koleksyon -->
        <div
          class="col-panel panel-mid"
          style="background-image: url('/img/img-collection2.png')"
        >
          <div class="col-panel-overlay"></div>
          <div class="col-panel-body col-panel-body-center">
            <div class="kuris-logo">></div>
            <h3 class="col-panel-title">Kuris Koleksyon</h3>
            <p class="col-panel-desc">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
              eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut
              enim ad minim veniam.
            </p>
            <a
              :href="
                brandPartner
                  ? route('store.brand-partner.collections', brandPartner.slug)
                  : '#'
              "
              class="col-panel-btn col-panel-btn-outline-dark"
              >VIEW COLLECTION</a
            >
          </div>
        </div>

        <!-- Panel 3: Discover CTA -->
        <div
          class="col-panel panel-orange"
          style="background-image: url('/img/img-indexcollection3.png')"
        >
          <div class="col-panel-overlay"></div>
          <div class="col-panel-body col-panel-body-center">
            <h3 class="col-panel-cta-title">
              Discover about <br />Our Collections
            </h3>
            <p class="col-panel-desc">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
              eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut
              enim ad minim veniam.
            </p>
            <a
              :href="
                brandPartner
                  ? route('store.brand-partner.collections', brandPartner.slug)
                  : '#'
              "
              class="col-panel-btn col-panel-btn-white"
              >VIEW OUR COLLECTIONS</a
            >
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Products Section -->
    <section class="featured-products-section">
      <div class="featured-products-inner">
        <h2 class="featured-products-title">Built Stronger. Made Better</h2>

        <ul class="featured-product-list" v-if="hasFeaturedProducts">
          <li
            v-for="product in featuredProductList"
            :key="'featured-' + product.id"
            class="featured-product-item"
            @mouseenter="hoverImageIndex[product.id] = true"
            @mouseleave="hoverImageIndex[product.id] = false"
          >
            <div class="featured-product-box">
              <!-- Badges -->
              <div class="featured-product-badges">
                <span
                  class="featured-badge-sale"
                  v-if="
                    product.compare_price &&
                    product.compare_price > product.price
                  "
                  >SALE</span
                >
                <span
                  class="featured-badge-new"
                  v-else-if="isNewProduct(product.created_at)"
                  >NEW</span
                >
              </div>

              <!-- Wishlist -->
              <button
                @click="toggleWishlist(product.id)"
                class="featured-wishlist-btn"
              >
                <i
                  :class="
                    wishlistIds.includes(product.id)
                      ? 'ri-heart-fill text-red-500'
                      : 'ri-heart-line'
                  "
                ></i>
              </button>

              <!-- Image -->
              <Link
                :href="route('store.brand-partner.product', product.slug)"
                class="featured-product-image-link"
              >
                <div class="featured-product-image">
                  <img
                    :src="
                      hoverImageIndex[product.id] &&
                      product.images &&
                      product.images.length > 1
                        ? product.images[1]?.url || product.image_url
                        : product.image_url || '/img/tshirt-placeholder.svg'
                    "
                    :alt="product.name"
                  />
                </div>
              </Link>

              <!-- Info -->
              <div class="featured-product-info">
                <p class="featured-product-collection">
                  <Link
                    v-if="product.collection"
                    :href="
                      route('store.brand-partner.shop', {
                        collection: product.collection.id,
                      })
                    "
                    class="text-muted text-decoration-none text-uppercase"
                    style="
                      font-size: 10px;
                      font-weight: 700;
                      letter-spacing: 0.5px;
                    "
                  >
                    {{ product.collection.label }}
                  </Link>
                </p>

                <Link
                  :href="route('store.brand-partner.product', product.slug)"
                  class="featured-product-name-link"
                >
                  <h5 class="featured-product-name">
                    {{ product.name }}
                  </h5>
                </Link>

                <!-- Stars -->
                <div class="featured-product-stars">
                  <i class="ri-star-fill"></i>
                  <i class="ri-star-fill"></i>
                  <i class="ri-star-fill"></i>
                  <i class="ri-star-fill"></i>
                  <i class="ri-star-fill"></i>
                </div>

                <!-- Price -->
                <div
                  class="featured-product-price-row d-flex align-items-center gap-2"
                >
                  <span class="featured-product-price">
                    {{ formatCurrency(product.price) }}
                  </span>
                  <span
                    v-if="
                      product.compare_price &&
                      product.compare_price > product.price
                    "
                    class="featured-product-old-price text-muted text-decoration-line-through"
                    style="font-size: 11px; margin-left: 4px"
                  >
                    {{ formatCurrency(product.compare_price) }}
                  </span>
                </div>

                <!-- Add to Cart -->
                <button
                  class="featured-atc-btn"
                  @click.prevent="addToCart(product)"
                  :disabled="!product.in_stock"
                >
                  {{ product.in_stock ? 'ADD TO CART' : 'OUT OF STOCK' }}
                </button>
              </div>
            </div>
          </li>
        </ul>

        <!-- View All -->
        <div class="featured-view-all-wrap" v-if="products.data.length > 0">
          <Link
            :href="route('store.brand-partner.shop', brandPartner.slug)"
            class="featured-view-all-btn"
          >
            VIEW ALL PRODUCTS
          </Link>
        </div>
      </div>
    </section>

    <!-- Reviews Section -->
    <section class="reviews-section">
      <div class="reviews-header">
        <h2 class="reviews-title">Read reviews,<br />Run with confidence</h2>
        <div class="reviews-nav">
          <button class="reviews-nav-btn" id="reviewsPrev">
            <i class="ri-arrow-left-line"></i>
          </button>
          <button
            class="reviews-nav-btn reviews-nav-btn-active"
            id="reviewsNext"
          >
            <i class="ri-arrow-right-line"></i>
          </button>
        </div>
      </div>

      <div class="reviews-track-wrap">
        <div class="reviews-track" id="reviewsTrack">
          <div class="review-card" v-for="review in reviews" :key="review.id">
            <p class="review-text">{{ review.comment }}</p>
            <div class="review-author">
              <img
                :src="review.avatar_url || '/img/avatar-placeholder.png'"
                :alt="review.name"
                class="review-avatar"
              />
              <div class="review-author-info">
                <span class="review-name">{{ review.name }}</span>
                <span class="review-role">{{ review.role }}</span>
              </div>
            </div>
          </div>

          <!-- Static fallback cards if no reviews prop -->
          <template v-if="!reviews || reviews.length === 0">
            <div class="review-card" v-for="n in 4" :key="n">
              <h4 class="review-headline">
                Cras amet ultricies pellentesque aliquam varius.
              </h4>
              <p class="review-text">
                Mauris id non nunc laoreet proin morbi faucibus id a. Donec
                gravida at sed auctor amet platea ac sed. Est tincidunt morbi
                tortor fermentum elementum platea. Erat id vestibulum duis
                turpis.
              </p>
              <div class="review-author">
                <img
                  src="/img/avatar-placeholder.png"
                  alt="Reviewer"
                  class="review-avatar"
                />
                <div class="review-author-info">
                  <span class="review-name">Iris Connelly</span>
                  <span class="review-role">42k Finisher</span>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </section>

    <!-- Marathon Countdown Section -->
    <section class="marathon-section">
      <div class="marathon-overlay"></div>
      <div class="marathon-content">
        <h2 class="marathon-title">
          Prepare for the
          <span class="marathon-highlight">Biggest<br />Marathon</span>
          of the Year
        </h2>
        <p class="marathon-desc">
          Eleifend nam ultrices sed ut ultrices. Nisi laoreet nulla posuere
          hendrerit. Etiam lectus mattis ultricies nunc aliquet a. Mattis nisi
          integer at diam amet sed sit.
        </p>

        <!-- Countdown Timer -->
        <div class="countdown-wrap">
          <div class="countdown-box">
            <span class="countdown-num">00</span>
            <span class="countdown-label">Days</span>
          </div>
          <div class="countdown-box">
            <span class="countdown-num">00</span>
            <span class="countdown-label">Hours</span>
          </div>
          <div class="countdown-box">
            <span class="countdown-num">00</span>
            <span class="countdown-label">Minutes</span>
          </div>
          <div class="countdown-box">
            <span class="countdown-num">00</span>
            <span class="countdown-label">Seconds</span>
          </div>
        </div>

        <p class="marathon-note">
          Note: Donec euismod lectus pellentesque mi neque turpis. Praesent
          adipiscing mauris ut ut vel nunc. Elit eu gravida ut sit.
        </p>

        <a href="#" class="marathon-btn">REGISTER NOW</a>
      </div>
    </section>

    <!-- What's Happening Section -->
    <section class="events-happening-section">
      <div class="events-happening-inner">
        <!-- Header -->
        <div class="events-happening-header">
          <h2 class="events-happening-title">What's Happening</h2>
          <div class="events-happening-tabs">
            <button
              class="events-tab-btn"
              :class="{ active: activeEventTab === 'upcoming' }"
              @click="activeEventTab = 'upcoming'"
            >
              UPCOMING EVENTS
            </button>
            <button
              class="events-tab-btn"
              :class="{ active: activeEventTab === 'past' }"
              @click="activeEventTab = 'past'"
            >
              PAST EVENTS
            </button>
          </div>
        </div>

        <!-- Events Scroll with Arrow Buttons -->
        <div class="events-scroll-container">
          <button
            class="events-arrow-btn events-arrow-left"
            id="eventsArrowLeft"
          >
            <i class="ri-arrow-left-s-line"></i>
          </button>

          <div class="events-scroll-wrap" id="eventsScrollWrap">
            <div class="events-scroll-track">
              <div class="event-card" v-for="n in 6" :key="n">
                <!-- Image with hover overlay -->
                <div class="event-card-image">
                  <div class="event-img-placeholder"></div>
                  <div class="event-card-hover-overlay">
                    <div class="event-hover-actions">
                      <a href="#" class="event-hover-btn">REGISTER</a>
                      <a
                        href="#"
                        class="event-hover-btn event-hover-btn-outline"
                        >VIEW EVENT INFO</a
                      >
                    </div>
                  </div>
                </div>

                <!-- Distance Tags -->
                <div class="event-tags">
                  <span class="event-tag">42KM</span>
                  <span class="event-tag">21KM</span>
                  <span class="event-tag">10KM</span>
                  <span class="event-tag">5KM</span>
                </div>

                <h3 class="event-card-title">Gensan Half Marathon 2026</h3>

                <div class="event-card-meta">
                  <div class="event-meta-row">
                    <div class="event-meta-icon">
                      <i class="ri-calendar-line"></i>
                    </div>
                    <span>Apr 19, 2026</span>
                  </div>
                  <div class="event-meta-row">
                    <div class="event-meta-icon">
                      <i class="ri-map-pin-line"></i>
                    </div>
                    <span>Gaisano Mall of Gensan, General Santos City</span>
                  </div>
                </div>

                <div class="event-card-actions">
                  <a href="#" class="event-action-link">REGISTER</a>
                  <a href="#" class="event-action-link">VIEW EVENT INFO</a>
                </div>
              </div>
            </div>
          </div>

          <button
            class="events-arrow-btn events-arrow-right"
            id="eventsArrowRight"
          >
            <i class="ri-arrow-right-s-line"></i>
          </button>
        </div>

        <!-- View All Button -->
        <div class="events-view-all-wrap">
          <a href="#" class="events-view-all-btn">VIEW ALL EVENTS</a>
        </div>
      </div>
    </section>

    <!-- Driven by Quality Section -->
    <section class="quality-section">
      <div class="quality-image">
        <img src="/img/img-team.png" alt="Tribu Pakaras Team" />
      </div>
      <div class="quality-content">
        <h2 class="quality-title">
          Driven by Quality.<br />Powered by Purpose.
        </h2>
        <p class="quality-desc">
          At Tribu Pakaras, we believe that what you wear should never hold you
          back. That's why we've invested in better technology, better
          processes, and better materials — so you can focus on pushing your
          limits.
        </p>
        <p class="quality-subdesc">
          We are committed to delivering products that match your performance.
        </p>
        <ul class="quality-list">
          <li>
            <i class="ri-checkbox-circle-line"></i>
            Enhanced Fabric Quality And Finishing
          </li>
          <li>
            <i class="ri-checkbox-circle-line"></i>
            Training And Activewear Collections
          </li>
          <li>
            <i class="ri-checkbox-circle-line"></i>
            Custom Team Uniforms
          </li>
          <li>
            <i class="ri-checkbox-circle-line"></i>
            Race Bibs And Event Merchandise
          </li>
          <li>
            <i class="ri-checkbox-circle-line"></i>
            Full Production Services For Brands And Events
          </li>
        </ul>
      </div>
    </section>

    <!-- Run Wild Section -->
    <section class="runwild-section">
      <h2 class="runwild-title">Run Wild. Live Pakaras.</h2>
      <div class="runwild-grid">
        <div class="runwild-item" v-for="n in 3" :key="n">
          <div class="runwild-card">
            <img
              src="/img/top_paper.png"
              alt=""
              class="tear-img tear-img--top"
            />
            <div class="photo-wrap">
              <img
                :src="`/img/img-section${n}.png`"
                :alt="`Section ${n}`"
                class="photo"
              />
            </div>
            <img
              src="/img/buttom_paper.png"
              alt=""
              class="tear-img tear-img--bottom"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Cart Bottom Bar -->
    <div class="product-cart-box" v-if="cartCount > 0">
      <div>
        <div class="cart-bar-inner">
          <div class="cart-bar-info">
            <h5 class="cart-item-count">
              {{ cartCount }}
              {{ cartCount === 1 ? 'item' : 'items' }}
            </h5>
            <h4 class="cart-bar-title">View Cart</h4>
          </div>
          <Link
            :href="route('store.brand-partner.cart')"
            class="btn btn-grocery-primary cart-bar-btn"
          >
            View Cart <i class="ri-arrow-right-line"></i>
          </Link>
        </div>
      </div>
    </div>

    <!-- Add to Cart Modal -->
    <div
      class="modal fade"
      id="addToCartModal"
      tabindex="-1"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered modal-sm-fullwidth">
        <div class="modal-content grocery-modal-content" v-if="selectedProduct">
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
                :src="
                  selectedProduct.image_url || '/img/tshirt-placeholder.svg'
                "
                :alt="selectedProduct.name"
                class="modal-product-img"
              />
              <div class="modal-product-info">
                <p v-if="selectedProduct.short_description">
                  {{ truncate(selectedProduct.short_description, 80) }}
                </p>
                <h5 class="modal-product-price">
                  {{ formatCurrency(selectedProduct.price) }}
                </h5>
                <span
                  v-if="
                    selectedProduct.compare_price &&
                    selectedProduct.compare_price > selectedProduct.price
                  "
                  class="old-price"
                >
                  {{ formatCurrency(selectedProduct.compare_price) }}
                </span>
              </div>
            </div>

            <!-- Garment / Color Variations -->
            <div
              v-if="selectedProduct?.colors_array?.length > 0"
              class="variation-block"
            >
              <div class="variation-heading">COLOR</div>
              <div class="pill-group">
                <button
                  v-for="color in selectedProduct.colors_array"
                  :key="color"
                  type="button"
                  class="pill-btn"
                  :class="{ active: selectedColor === color }"
                  @click="
                    selectedColor = selectedColor === color ? null : color
                  "
                >
                  {{ color }}
                </button>
              </div>
            </div>

            <!-- Size Variations -->
            <div
              v-if="selectedProduct?.sizes_array?.length > 0"
              class="variation-block"
            >
              <div class="variation-heading">SIZE</div>
              <div class="pill-group">
                <button
                  v-for="size in selectedProduct.sizes_array"
                  :key="size"
                  type="button"
                  class="pill-btn"
                  :class="{ active: selectedSize === size }"
                  @click="selectedSize = selectedSize === size ? null : size"
                >
                  {{ size }}
                </button>
              </div>
            </div>

            <p
              v-if="
                (selectedProduct?.colors_array?.length > 0 && !selectedColor) ||
                (selectedProduct?.sizes_array?.length > 0 && !selectedSize)
              "
              class="variation-hint mt-2 mb-3"
            >
              Please select required variations to continue.
            </p>

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
                {{ formatCurrency(selectedProduct.price * modalQuantity) }}
              </h4>
            </div>
            <button
              class="btn btn-grocery-primary cart-bar-btn"
              @click="confirmAddToCart"
              :disabled="
                isAddingToCart ||
                (selectedProduct?.colors_array?.length > 0 && !selectedColor) ||
                (selectedProduct?.sizes_array?.length > 0 && !selectedSize)
              "
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
  </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Modal } from 'bootstrap';
import { emitter } from '@/composables/eventBus';
import ToastComponent from '@/components/ToastContainer.vue';

const activeEventTab = ref('upcoming');
let countdownInterval = null;

const props = defineProps({
  brandPartner: Object,
  products: Object,
  categories: Array,
  events: Array,
  reviews: {
    type: Array,
    default: () => [],
  },
  cartCount: {
    type: Number,
    default: 0,
  },
  featuredProducts: {
    type: Array,
    default: () => [],
  },
  filter: {
    type: Object,
    default: () => ({}),
  },
  wishlistIds: {
    type: Array,
    default: () => [],
  },
  auth: Object,
});

const selectedCategory = ref(props.filter.category || null);
const selectedEvent = ref(props.filter.event || null);
const searchQuery = ref(props.filter.search || '');
let searchTimeout = null;

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
  }).format(amount / 100);
};

const truncate = (text, length) => {
  if (!text) return '';
  return text.length > length ? text.substring(0, length) + '...' : text;
};

const selectCategory = (category) => {
  selectedCategory.value = category.id;
  selectedEvent.value = null;
  applyFilters();
};

const selectEvent = (event) => {
  selectedEvent.value = event.id;
  applyFilters();
};

const clearFilters = () => {
  selectedCategory.value = null;
  selectedEvent.value = null;
  searchQuery.value = '';
  applyFilters();
};

const applyFilters = () => {
  const params = {};
  if (selectedCategory.value) params.category = selectedCategory.value;
  if (selectedEvent.value) params.event = selectedEvent.value;
  if (searchQuery.value) params.search = searchQuery.value;
  router.get(route('store.brand-partner.index'), params, {
    preserveState: true,
    replace: true,
  });
};

const hoverImageIndex = ref({});
const selectedProduct = ref(null);
const modalQuantity = ref(1);
const selectedColor = ref(null);
const selectedSize = ref(null);
const isAddingToCart = ref(false);
let cartModal = null;

const featuredProductList = computed(() => {
  return props.featuredProducts.length > 0
    ? props.featuredProducts
    : props.products.data.slice(0, 4);
});

const hasFeaturedProducts = computed(() => {
  return featuredProductList.value.length > 0;
});

//Produst Badge
function isNewProduct(createdAt) {
  const dateCreated = new Date(createdAt);
  const now = new Date();

  const diffTime = now - dateCreated;
  const msPerDay = 1000 * 60 * 60 * 24;
  const diffDays = diffTime / msPerDay;

  return diffDays <= 7;
}

onMounted(() => {
  // Cart modal
  const modalEl = document.getElementById('addToCartModal');
  if (modalEl) {
    cartModal = new Modal(modalEl);
    modalEl.addEventListener('hidden.bs.modal', () => {
      selectedProduct.value = null;
      modalQuantity.value = 1;
      selectedColor.value = null;
      selectedSize.value = null;
    });
  }

  // Reviews slider
  const track = document.getElementById('reviewsTrack');
  const prevBtn = document.getElementById('reviewsPrev');
  const nextBtn = document.getElementById('reviewsNext');
  let reviewIndex = 0;

  const getCardWidth = () => {
    const card = track?.querySelector('.review-card');
    return card ? card.offsetWidth + 20 : 400;
  };

  nextBtn?.addEventListener('click', () => {
    const maxIndex = (track?.children.length || 0) - 3;
    if (reviewIndex < maxIndex) {
      reviewIndex++;
      track.style.transform = `translateX(-${reviewIndex * getCardWidth()}px)`;
    }
  });

  prevBtn?.addEventListener('click', () => {
    if (reviewIndex > 0) {
      reviewIndex--;
      track.style.transform = `translateX(-${reviewIndex * getCardWidth()}px)`;
    }
  });

  // Countdown timer
  const targetDate = new Date('2026-12-31T00:00:00').getTime();
  const updateCountdown = () => {
    const now = new Date().getTime();
    const diff = targetDate - now;
    if (diff <= 0) return;
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    const pad = (n) => String(n).padStart(2, '0');
    const el = (id) => document.getElementById(id);
    if (el('cd-days')) el('cd-days').textContent = pad(days);
    if (el('cd-hours')) el('cd-hours').textContent = pad(hours);
    if (el('cd-minutes')) el('cd-minutes').textContent = pad(minutes);
    if (el('cd-seconds')) el('cd-seconds').textContent = pad(seconds);
  };
  updateCountdown();
  countdownInterval = setInterval(updateCountdown, 1000);

  // Events slider
  const eventsTrack = document.querySelector('.events-scroll-track');
  const eventsArrowLeft = document.getElementById('eventsArrowLeft');
  const eventsArrowRight = document.getElementById('eventsArrowRight');
  let eventsIndex = 0;

  const getEventCardWidth = () => {
    const card = eventsTrack?.querySelector('.event-card');
    return card ? card.offsetWidth + 24 : 364;
  };

  const updateEventsArrows = () => {
    const maxIndex = (eventsTrack?.children.length || 0) - 3;
    if (eventsArrowLeft) eventsArrowLeft.disabled = eventsIndex <= 0;
    if (eventsArrowRight) eventsArrowRight.disabled = eventsIndex >= maxIndex;
  };

  eventsArrowRight?.addEventListener('click', () => {
    const maxIndex = (eventsTrack?.children.length || 0) - 3;
    if (eventsIndex < maxIndex) {
      eventsIndex++;
      eventsTrack.style.transform = `translateX(-${eventsIndex * getEventCardWidth()}px)`;
      updateEventsArrows();
    }
  });

  eventsArrowLeft?.addEventListener('click', () => {
    if (eventsIndex > 0) {
      eventsIndex--;
      eventsTrack.style.transform = `translateX(-${eventsIndex * getEventCardWidth()}px)`;
      updateEventsArrows();
    }
  });

  updateEventsArrows();
});

onBeforeUnmount(() => {
  clearTimeout(searchTimeout);
  clearInterval(countdownInterval);
  if (cartModal) {
    cartModal.dispose();
    cartModal = null;
  }
});

const addToCart = (product) => {
  selectedProduct.value = product;
  modalQuantity.value = 1;
  selectedColor.value = null;
  selectedSize.value = null;
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
      color: selectedColor.value,
      size: selectedSize.value,
    },
    {
      preserveScroll: true,

      onSuccess: () => {
        cartModal?.hide();

        emitter.emit('toast:show', {
          type: 'success',
          message: `${selectedProduct.value.name} added to cart!`,
        });
      },

      onError: (errors) => {
        console.error('Error adding to cart:', errors);

        emitter.emit('toast:show', {
          type: 'error',
          message: 'Failed to add item to cart',
        });
      },

      onFinish: () => {
        isAddingToCart.value = false;
      },
    },
  );
};

const toggleWishlist = (productId) => {
  // Check if user is logged in (from page props)
  const user = props.auth?.user;
  if (!user) {
    // Trigger login modal via custom event
    window.dispatchEvent(new CustomEvent('open-login-modal'));
    return;
  }

  router.post(
    route('store.brand-partner.wishlist.toggle'),
    { product_id: productId },
    {
      preserveScroll: true,
      onFinish: () => {
        router.reload({ only: ['wishlistIds'] });
      },
    },
  );
};
</script>

<style scoped>
/* ===== Animation Variables ===== */
:root {
  --animation-timing-unit: 80ms;
  --animation-timing-300: calc(var(--animation-timing-unit) * 3);
  --ease-out-quart: cubic-bezier(0.165, 0.84, 0.44, 1);
}

/* ===== Hero Carousel Styles ===== */
.hero-carousel-section {
  width: 100vw;
  position: relative;
  left: 50%;
  right: 50%;
  margin-left: -50vw;
  margin-right: -50vw;
  margin-top: 0;
  margin-bottom: 0;
  background-color: #1a1a1a;
  overflow-x: hidden;
}

.hero-carousel-section .carousel,
.hero-carousel-section .carousel-inner,
.hero-carousel-section .carousel-item {
  width: 100%;
}

.hero-carousel .carousel-item {
  height: 100vh;
  min-height: 600px;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  position: relative;
  overflow: hidden;
}

.slide-layout {
  max-width: 1400px;
  margin: 0 auto;
  height: 100%;
  display: flex;
  align-items: center;
  position: relative;
  padding: 0 40px;
  padding-top: 80px;
}

.slide-content-left {
  flex: 1;
  max-width: 600px;
}

.slide-content-right {
  flex: 1;
  display: flex;
  justify-content: flex-end;
  align-items: center;
  height: 100%;
}

.slide-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 800;
  font-size: 3.5rem;
  line-height: 1.1;
  letter-spacing: -1px;
  margin-bottom: 20px;
  text-transform: uppercase;
}

.slide-subtitle {
  font-family: 'Poppins', sans-serif;
  font-weight: 500;
  font-style: normal;
  font-size: 16px;
  line-height: 26px;
  letter-spacing: 0;
  margin-bottom: 30px;
  max-width: 500px;
}

.slide-btn-outline {
  border: 2px solid #fff;
  color: #fff;
  padding: 12px 30px;
  border-radius: 0;
  font-weight: 600;
  text-transform: uppercase;
  transition: all 0.3s ease;
  background: transparent;
}

.slide-btn-outline:hover {
  background: rgba(var(--grocery-primary), 1);
  border-color: rgba(var(--grocery-primary), 1);
  color: #000;
}

/* Slide 1 specifics */
.slide-1-bg {
  background-image: url('/img/img-carousel2.png');
  background-size: cover;
  background-position: center;
}

.empty-product-image-container {
  width: 400px;
  height: 450px;
  background: rgba(255, 255, 255, 0.05);
  border: 2px dashed rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Slide 2 specifics */
.slide-2-bg {
  background-image: url('/img/img-carousel1.png');
  background-size: cover;
  background-position: center;
}
.slide-overlay-left-dark {
  position: absolute;
  top: 0;
  left: 0;
  width: 60%;
  height: 100%;
  background: linear-gradient(
    90deg,
    rgba(0, 0, 0, 0.8) 0%,
    rgba(0, 0, 0, 0.6) 60%,
    rgba(0, 0, 0, 0) 100%
  );
  z-index: 1;
}

/* Slide 3 specifics */
.slide-3-bg {
  background-image: url('/img/img-carousel4.png');
  background-size: cover;
  background-position: center;
}

/* Slide 4 specifics */
.slide-4-bg {
  background-image: url('/img/img-carousel3.png');
  background-size: cover;
  background-position: center;
}
.slide-overlay-left-orange {
  position: absolute;
  top: 0;
  left: 0;
  width: 55%;
  height: 100%;
  background: linear-gradient(110deg, #ec4e20 85%, transparent 85%);
  z-index: 1;
}

/* Carousel Indicators */
.hero-carousel .carousel-indicators {
  position: absolute;
  bottom: 25px;
  transform: translateX(-50%);
  display: flex;
  align-items: center;
  gap: 10px;
  left: 50%;
  padding: 8px 16px;
  border-radius: 50px;
  margin: 0;
  right: auto;
}

.hero-carousel .carousel-indicators button {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  border: none;
  background-color: rgba(255, 255, 255, 0.4);
  transition: all 0.3s ease;
}

.hero-carousel .carousel-indicators button.active {
  width: 28px;
  height: 6px;
  border-radius: 10px;
  background-color: #fff;
}

/* ===== Grocery Template Styles ===== */
.variation-block {
  margin-top: 15px;
  margin-bottom: 20px;
}
.variation-heading {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.12em;
  color: #555;
  text-transform: uppercase;
  margin-bottom: 10px;
}
.pill-group {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
.pill-btn {
  padding: 7px 18px;
  border: 1px solid #d1d1d1;
  background: #fff;
  border-radius: 2px;
  font-size: 13px;
  font-weight: 500;
  color: #1a1a1a;
  cursor: pointer;
  transition: all 0.15s;
  letter-spacing: 0.02em;
  text-transform: uppercase;
}
.pill-btn:hover {
  border-color: #1a1a1a;
  background: #f9f9f9;
}
.pill-btn.active {
  border-color: #ff9505;
  background: #ff9505;
  color: #fff;
}
.variation-hint {
  font-size: 12px;
  color: #dc2626;
  margin: -8px 0 16px;
}

.grocery-store-page {
  font-family: 'Public Sans', sans-serif;
  min-height: 100vh;
  overflow-x: visible;
  max-width: 100%;
  /* Grocery Theme Color Variables */
  --grocery-theme: 255, 149, 5, 1; /* Main teal/cyan color: rgb(60, 133, 153) */
  --grocery-content: 143, 143, 178; /* Light gray-blue content text */
  --grocery-title: 27, 27, 62; /* Dark blue-gray for titles */
  --grocery-border: 232, 232, 232; /* Light gray borders */
  --grocery-primary: 254, 175, 24; /* Yellow/orange accent */
  --grocery-light-bg: 247, 247, 247; /* Light gray background */
  --grocery-rating: 255, 191, 19; /* Gold/yellow for ratings */
}

/* Responsive adjustments */
@media (max-width: 991px) {
  .slide-product-image,
  .slide-runner-image {
    max-width: 300px;
  }
}

/* ===== Search Section - form-style-7 ===== */
.grocery-search-section {
  padding: 16px 0 8px;
  background: #fff;
}

.search-box {
  margin-bottom: 0;
}

.form-style-7 .search-input-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.form-style-7 .search-icon {
  position: absolute;
  left: 16px;
  font-size: 20px;
  color: #9e9e9e;
  z-index: 1;
}

.form-style-7 .form-control {
  width: 100%;
  padding: 14px 48px 14px 48px;
  border: 2px solid #eeeeee;
  border-radius: 16px;
  font-size: 14px;
  font-family: 'Public Sans', sans-serif;
  color: #333;
  background: #fafafa;
  outline: none;
  transition: all 0.25s ease;
}

.form-style-7 .form-control:focus {
  border-color: rgb(var(--grocery-theme));
  background: #fff;
  box-shadow: 0 0 0 4px rgba(var(--grocery-theme), 0.08);
}

.form-style-7 .form-control::placeholder {
  color: #bdbdbd;
}

.clear-search-btn {
  position: absolute;
  right: 14px;
  background: none;
  border: none;
  color: #9e9e9e;
  font-size: 20px;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  transition: color 0.2s;
}

.clear-search-btn:hover {
  color: rgb(var(--grocery-theme));
}

/* ===== Category Section - grocery-category-box ===== */
.grocery-category-section {
  padding: 16px 0 12px;
  background: #fff;
}

.grocery-category-slider {
  overflow: hidden;
}

.category-scroll-wrap {
  display: flex;
  gap: 14px;
  overflow-x: auto;
  scrollbar-width: none;
  -ms-overflow-style: none;
  -webkit-overflow-scrolling: touch;
  padding: 4px 0 8px;
}

.category-scroll-wrap::-webkit-scrollbar {
  display: none;
}

.grocery-category-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  min-width: 76px;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.25s ease;
  flex-shrink: 0;
}

.grocery-category-box .category-icon-wrap {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  background: rgba(var(--grocery-theme), 0.05);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.25s ease;
  border: 2px solid transparent;
  box-shadow: 0 2px 8px rgba(var(--grocery-theme), 0.06);
}

.grocery-category-box.active .category-icon-wrap {
  background: rgb(var(--grocery-theme));
  border-color: rgb(var(--grocery-theme));
  box-shadow: 0 4px 14px rgba(var(--grocery-theme), 0.3);
}

.grocery-category-box .category-icon-wrap i {
  font-size: 26px;
  color: rgb(var(--grocery-theme));
  transition: color 0.25s ease;
}

.grocery-category-box.active .category-icon-wrap i {
  color: #fff;
}

.grocery-category-box h5 {
  font-size: 11px;
  font-weight: 600;
  color: rgb(var(--grocery-content));
  margin: 0;
  white-space: nowrap;
  text-align: center;
  letter-spacing: 0.2px;
}

.grocery-category-box.active h5 {
  color: rgb(var(--grocery-theme));
  font-weight: 700;
}

/* ===== Event Tabs - Modern Style ===== */
.grocery-events-section {
  padding: 16px 0 8px;
  background: #ffffff;
  border-bottom: 1px solid rgba(0, 0, 0, 0.04);
  position: sticky;
  top: 0;
  z-index: 10;
  backdrop-filter: blur(8px);
  background: rgba(255, 255, 255, 0.95);
}

.tab-style-5 {
  display: flex;
  gap: 10px;
  overflow-x: auto;
  scrollbar-width: none;
  -ms-overflow-style: none;
  border-bottom: none;
  padding: 4px 4px 8px;
  margin: 0;
  list-style: none;
  scroll-behavior: smooth;
}

.tab-style-5::-webkit-scrollbar {
  display: none;
}

.tab-style-5 .nav-item {
  flex-shrink: 0;
}

.tab-style-5 .nav-link {
  padding: 10px 24px;
  border-radius: 30px;
  border: 1.5px solid #e5e7eb;
  background: #ffffff;
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.01em;
  color: #4b5563;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-family:
    'Public Sans',
    system-ui,
    -apple-system,
    sans-serif;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
  position: relative;
  overflow: hidden;
}

.tab-style-5 .nav-link::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(var(--grocery-theme), 0.1);
  transform: translate(-50%, -50%);
  transition:
    width 0.5s,
    height 0.5s;
}

.tab-style-5 .nav-link:hover::before {
  width: 300px;
  height: 300px;
}

.tab-style-5 .nav-link:hover {
  border-color: rgb(var(--grocery-theme));
  color: rgb(var(--grocery-theme));
  background: #ffffff;
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(var(--grocery-theme), 0.12);
}

.tab-style-5 .nav-link.active {
  background: linear-gradient(
    135deg,
    rgb(var(--grocery-theme)) 0%,
    rgba(var(--grocery-theme), 0.9) 100%
  );
  border-color: rgb(var(--grocery-theme));
  color: #ffffff;
  box-shadow: 0 6px 16px rgba(var(--grocery-theme), 0.3);
  transform: scale(1.02);
}

.tab-style-5 .nav-link.active:hover {
  transform: scale(1.02) translateY(-2px);
  box-shadow: 0 8px 20px rgba(var(--grocery-theme), 0.35);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .grocery-events-section {
    padding: 12px 0 6px;
  }

  .tab-style-5 {
    gap: 8px;
    padding: 4px 2px 6px;
  }

  .tab-style-5 .nav-link {
    padding: 8px 18px;
    font-size: 13px;
  }
}

/* Touch device optimization */
@media (hover: none) and (pointer: coarse) {
  .tab-style-5 .nav-link {
    padding: 10px 20px;
    min-height: 44px;
    display: flex;
    align-items: center;
  }

  .tab-style-5 .nav-link::before {
    display: none;
  }
}

/* ===== Products Section ===== */
.grocery-products-section {
  padding: 20px 0 24px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.section-title {
  font-size: 18px;
  font-weight: 800;
  color: rgb(var(--grocery-title));
  margin: 0;
  font-family: 'Public Sans', sans-serif;
}

.product-count {
  font-size: 13px;
  color: rgb(var(--grocery-content));
  font-weight: 500;
}

/* ===== Products Section - New Design ===== */
.grocery-products-section {
  padding: 40px 0;
}

.section-title-main {
  font-size: 64px;
  font-weight: 800;
  text-align: center;
  margin-bottom: 40px;
  color: #535353;
  letter-spacing: -1px;
  line-height: 1.2;
  font-family: 'Arial', 'Helvetica', sans-serif;
  display: block;
  width: 100%;
}

/* Category Pills */
.category-pills-wrapper {
  margin-bottom: 30px;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.category-pills-scroll {
  display: flex;
  gap: 10px;
  justify-content: center;
  flex-wrap: wrap;
}

.category-pill {
  padding: 8px 20px;
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 30px;
  font-size: 13px;
  font-weight: 600;
  color: #666;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.category-pill:hover {
  border-color: #333;
  color: #333;
}

.category-pill.active {
  background: #1a1a1a;
  border-color: #1a1a1a;
  color: #fff;
}

/* Products Grid */
.products-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  margin-bottom: 40px;
}

@media (min-width: 576px) {
  .products-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 992px) {
  .products-grid {
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
  }
}

/* Product Card */
.product-card {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  position: relative;
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

/* Sale Badge */
.product-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  background: #e53935;
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 4px;
  z-index: 2;
  text-transform: uppercase;
}

/* Product Image */
.product-image-link {
  display: block;
  text-decoration: none;
}

.product-image-wrapper {
  aspect-ratio: 1 / 1;
  overflow: hidden;
  background: #f8f8f8;
}

.product-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-img {
  transform: scale(1.05);
}

/* Product Info */
.product-info {
  padding: 14px;
}

.product-name {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 4px;
  line-height: 1.3;
}

.product-collection {
  font-size: 12px;
  color: #999;
  margin: 0 0 8px;
  letter-spacing: 0.3px;
}

/* Rating Stars */
.product-rating {
  display: flex;
  gap: 3px;
  margin-bottom: 8px;
}

.product-rating i {
  font-size: 12px;
  color: #ffc107;
}

/* Pricing */
.product-pricing {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}

.product-price {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
}

.product-old-price {
  font-size: 13px;
  color: #bbb;
  text-decoration: line-through;
}

/* Add to Cart Button */
.add-to-cart-btn {
  width: 100%;
  padding: 10px;
  background: #1a1a1a;
  border: none;
  color: #fff;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  cursor: pointer;
  transition: background 0.2s ease;
  border-radius: 6px;
}

.add-to-cart-btn:hover {
  background: #333;
}

.add-to-cart-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

/* View All Button */
.view-all-wrapper {
  text-align: center;
  margin-top: 20px;
}

.view-all-btn {
  display: inline-block;
  padding: 12px 32px;
  background: transparent;
  border: 2px solid #198754;
  color: #198754;
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  text-decoration: none;
  transition: all 0.2s ease;
}

.view-all-btn:hover {
  background: #ff9505;
  border: 2px solid #ff9505;
  color: #fff;
}

/* ===== Empty State ===== */
.grocery-empty-state {
  text-align: center;
  padding: 60px 20px;
  background: #fff;
  border-radius: 20px;
}

.empty-icon-circle {
  width: 100px;
  height: 100px;
  background: rgba(var(--grocery-theme), 0.1);
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

.empty-icon-circle i {
  font-size: 42px;
  color: rgb(var(--grocery-theme));
}

.grocery-empty-state h4 {
  font-weight: 800;
  color: rgb(var(--grocery-title));
  margin-bottom: 8px;
  font-family: 'Public Sans', sans-serif;
}

.grocery-empty-state p {
  color: rgb(var(--grocery-content));
  margin-bottom: 20px;
  font-size: 14px;
}

/* ===== Primary Button ===== */
.btn-grocery-primary {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 10px 22px;
  color: #06402b;
  border: 1 solid #06402b;
  border-radius: 0;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.25s ease;
  font-family: 'Public Sans', sans-serif;
  text-decoration: none;
}

.btn-grocery-primary:hover {
  background-color: #06402b;
  box-shadow: 0 4px 16px rgba(var(--grocery-theme), 0.35);
  color: #fff;
  transform: translateY(-1px);
}

.btn-grocery-primary:active {
  transform: translateY(0);
}

.btn-grocery-primary:disabled {
  opacity: 0.65;
  cursor: not-allowed;
  transform: none;
}

.btn-grocery-primary i {
  font-size: 16px;
}

/* ===== Pagination ===== */
.grocery-pagination {
  display: flex;
  justify-content: center;
  margin-top: 28px;
  padding-bottom: 16px;
}

.pagination {
  gap: 4px;
}

.pagination .page-link {
  border: none;
  color: #616161;
  border-radius: 12px;
  font-weight: 600;
  font-size: 13px;
  padding: 8px 14px;
  font-family: 'Public Sans', sans-serif;
  transition: all 0.2s ease;
}

.pagination .page-item.active .page-link {
  background: rgb(var(--grocery-theme));
  color: #fff;
  box-shadow: 0 2px 8px rgba(var(--grocery-theme), 0.3);
}

.pagination .page-link:hover {
  background: rgba(var(--grocery-theme), 0.1);
  color: rgb(var(--grocery-theme));
}

/* ===== Collections Section ===== */
.collections-section {
  width: 100vw;
  position: relative;
  left: 50%;
  margin-left: -50vw;
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 0; /* change from -80px back to 0 */
  z-index: 1;
}

/* --- DREAMER BANNER --- */
.dreamer-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-height: 420px;
  padding: 60px 80px;
  position: relative;
  overflow: hidden;
  gap: 40px;
}

/* Overlay to ensure text readability over the background image */
.dreamer-banner::before {
  content: '';
  position: absolute;
  inset: 0;
  pointer-events: none;
  z-index: 1;
}

.dreamer-banner-images {
  display: flex;
  align-items: flex-end;
  gap: 20px;
  flex-shrink: 0;
  z-index: 2;
}

.dreamer-content {
  flex: 1;
  z-index: 2;
  text-align: right;
  max-width: 500px;
  margin-left: auto;
}

.dreamer-label {
  display: block;
  font-size: 13px;
  letter-spacing: 6px;
  color: rgba(255, 255, 255, 0.7);
  font-weight: 400;
  margin-bottom: 4px;
  text-transform: uppercase;
}

.dreamer-title {
  font-size: 72px;
  font-weight: 900;
  color: #fff;
  letter-spacing: 8px;
  margin: 0 0 20px;
  line-height: 1;
  font-family: 'Public Sans', sans-serif;
  text-transform: uppercase;
}

.dreamer-description {
  font-size: 13px;
  line-height: 1.75;
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 12px;
}

.dreamer-description strong {
  color: #fff;
  font-weight: 700;
}

/* "SPACE FOR ADVENTURE" accent text */
.dreamer-accent {
  display: block;
  font-size: 11px;
  letter-spacing: 4px;
  color: rgba(255, 255, 255, 0.5);
  text-transform: uppercase;
  margin: 16px 0 24px;
}

.dreamer-btn {
  display: inline-block;
  border: 1.5px solid rgba(255, 255, 255, 0.5);
  color: #fff;
  padding: 11px 26px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  text-decoration: none;
  transition: all 0.3s;
}

.dreamer-btn:hover {
  background: #fff;
  color: #ec4e1f;
  border-color: #fff;
}

/* --- 3 PANEL GRID --- */
.collections-panels {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  min-height: 460px;
  gap: 8px;
}

.col-panel {
  position: relative;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  overflow: hidden;
}

.col-panel-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to top,
    rgba(0, 0, 0, 0.75) 0%,
    rgba(0, 0, 0, 0.3) 50%,
    rgba(0, 0, 0, 0.1) 100%
  );
  z-index: 1;
}

.panel-dark {
  background-color: #1a1a1a;
}

.panel-mid {
  background-color: #d0d0d0;
}

.panel-orange {
  background-color: #e84b0f;
}

.col-panel-body {
  position: relative;
  z-index: 2;
  padding: 32px 28px;
}

.col-panel-body-center {
  display: flex;
  flex-direction: column;
  justify-content: center;
  height: 100%;
  padding: 40px 32px;
}

.panel-mid .col-panel-body-center,
.panel-orange .col-panel-body-center {
  position: absolute;
  inset: 0;
  z-index: 2;
}

/* Kuris logo text */

.kuris-script {
  font-family: 'Georgia', serif;
  font-style: italic;
  font-size: 40px;
  color: #ffffff;
  line-height: 1.1;
}

.kuris-script-bold {
  font-size: 52px;
  font-weight: 900;
  font-style: italic;
  color: #ffffff;
}

.col-panel-title {
  font-size: 16px;
  font-weight: 800;
  margin: 0 0 10px;
  font-family: 'Public Sans', sans-serif;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-top: 220px;
}

.panel-dark .col-panel-title {
  color: #fff;
}

.panel-mid .col-panel-title {
  color: #ffffff;
}

.panel-orange .col-panel-title {
  display: none;
}

.col-panel-cta-title {
  font-size: 26px;
  font-weight: 800;
  color: #fff;
  margin: 0 0 14px;
  line-height: 1.3;
  font-family: 'Public Sans', sans-serif;
  margin-top: 200px;
}

.col-panel-desc {
  font-size: 12.5px;
  line-height: 1.6;
  margin: 0 0 20px;
}

.panel-dark .col-panel-desc {
  color: rgba(255, 255, 255, 0.75);
}

.panel-mid .col-panel-desc {
  color: #ffffff;
}

.panel-orange .col-panel-desc {
  color: rgba(255, 255, 255, 0.85);
}

/* Panel buttons */
.col-panel-btn {
  display: inline-block;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  padding: 9px 18px;
  text-decoration: none;
  width: fit-content;
  transition: all 0.25s;
  font-family: 'Public Sans', sans-serif;
}

.col-panel-btn-outline {
  border: 1.5px solid rgba(255, 255, 255, 0.7);
  color: #fff;
  background: transparent;
}

.col-panel-btn-outline:hover {
  background: #fff;
  border-color: #fff;
  color: #ec4e1f;
}

.col-panel-btn-outline-dark {
  border: 1.5px solid #ffffff;
  color: #ffffff;
  background: transparent;
}

.col-panel-btn-outline-dark:hover {
  background: #ffffff;
  color: #ec4e1f;
}

.col-panel-btn-white {
  border: 1.5px solid rgba(255, 255, 255, 0.8);
  color: #fff;
  background: transparent;
}

.col-panel-btn-white:hover {
  background: #fff;
  color: #e84b0f;
}

/* ===== Responsive ===== */
@media (max-width: 991px) {
  .dreamer-banner {
    padding: 50px 40px;
    min-height: auto;
  }

  .dreamer-title {
    font-size: 48px;
  }

  .collections-panels {
    grid-template-columns: 1fr 1fr;
    min-height: auto;
  }

  .panel-orange {
    grid-column: span 2;
    min-height: 220px;
  }
}

@media (max-width: 768px) {
  .dreamer-banner {
    flex-direction: column;
    text-align: center;
    padding: 40px 24px;
  }

  .dreamer-content {
    text-align: center;
    margin-left: 0;
    max-width: 100%;
  }

  .dreamer-banner-images {
    justify-content: center;
  }

  .dreamer-title {
    font-size: 40px;
  }

  .collections-panels {
    grid-template-columns: 1fr;
    min-height: unset;
  }

  .panel-orange {
    grid-column: span 1;
  }

  .col-panel {
    min-height: 320px;
  }

  .panel-mid .col-panel-body-center,
  .panel-orange .col-panel-body-center {
    position: relative;
    inset: unset;
  }
}

/* ===== Reviews Section ===== */
.reviews-section {
  width: 100vw;
  position: relative;
  left: 50%;
  margin-left: -50vw;
  background-color: #e84b0f;
  background-image: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Cpath d='M40 0C17.9 0 0 17.9 0 40s17.9 40 40 40 40-17.9 40-40S62.1 0 40 0zm0 60c-11 0-20-9-20-20s9-20 20-20 20 9 20 20-9 20-20 20z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  padding: 60px 0 70px;
  overflow: hidden;
  z-index: 1;
  margin-top: 8px;
}

.reviews-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 0 60px;
  margin-bottom: 40px;
}

.reviews-title {
  font-size: 36px;
  font-weight: 800;
  color: #fff;
  line-height: 1.25;
  margin: 0;
  font-family: 'Public Sans', sans-serif;
}

.reviews-nav {
  display: flex;
  gap: 12px;
  align-items: center;
  padding-top: 8px;
}

.reviews-nav-btn {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 0.5);
  background: transparent;
  color: #fff;
  font-size: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.25s;
}

.reviews-nav-btn:hover {
  background: #e09515;
  border-color: #e09515;
}

.reviews-nav-btn-active {
  border: 2px solid rgba(255, 255, 255, 0.5);
  background: transparent;
  color: #fff;
}

.reviews-nav-btn-active:hover {
  background: #e09515;
  border-color: #e09515;
}

/* Track */
.reviews-track-wrap {
  overflow: hidden;
  padding: 0 60px;
}

.reviews-track {
  display: flex;
  gap: 20px;
  transition: transform 0.4s ease;
}

/* Review Card */
.review-card {
  background: #fff;
  border-radius: 0;
  padding: 32px 28px;
  min-width: 380px;
  max-width: 380px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  flex-shrink: 0;
}

.review-headline {
  font-size: 16px;
  font-weight: 700;
  color: #111;
  margin: 0 0 16px;
  line-height: 1.4;
  font-family: 'Public Sans', sans-serif;
}

.review-text {
  font-size: 13px;
  color: #555;
  line-height: 1.7;
  margin: 0 0 24px;
  flex: 1;
}

.review-author {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-top: auto;
}

.review-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.review-author-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.review-name {
  font-size: 14px;
  font-weight: 700;
  color: #111;
  font-family: 'Public Sans', sans-serif;
}

.review-role {
  font-size: 12px;
  color: #888;
  font-weight: 400;
}

/* Responsive */
@media (max-width: 768px) {
  .reviews-header {
    padding: 0 24px;
    flex-direction: column;
    gap: 20px;
  }

  .reviews-track-wrap {
    padding: 0 24px;
  }

  .reviews-title {
    font-size: 26px;
  }

  .review-card {
    min-width: 300px;
    max-width: 300px;
    padding: 24px 20px;
  }
}

/* ===== Featured Products Section ===== */
.featured-products-section {
  width: 100vw;
  position: relative;
  left: 50%;
  margin-left: -50vw;
  background: #fff;
  padding: 70px 0 80px;
  z-index: 1;
  margin-top: 8px;
}

.featured-products-inner {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 40px;
}

.featured-products-title {
  font-size: 48px;
  font-weight: 800;
  color: #535353;
  text-align: center;
  margin: 0 0 48px;
  font-family: 'Arial', 'Helvetica', sans-serif;
  letter-spacing: -1px;
}

/* Grid */
.featured-product-list {
  list-style: none;
  padding: 0;
  margin: 0 0 48px;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

.featured-product-item {
  display: flex;
}

/* Card */
.featured-product-box {
  width: 100%;
  position: relative;
  background: #fff;
  display: flex;
  flex-direction: column;
  transition:
    transform 0.25s,
    box-shadow 0.25s;
}

.featured-product-box:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 28px rgba(0, 0, 0, 0.09);
}

/* Badges */
.featured-product-badges {
  position: absolute;
  top: 10px;
  left: 0;
  z-index: 2;
}

.featured-badge-sale {
  display: inline-block;
  background: #ff5722;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 5px 12px 5px 10px;
  border-radius: 0 50px 50px 0;
  letter-spacing: 0.5px;
}

.featured-badge-new {
  display: inline-block;
  background: #1976d2;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 5px 12px 5px 10px;
  border-radius: 0 50px 50px 0;
  letter-spacing: 0.5px;
}

/* Wishlist */
.featured-wishlist-btn {
  position: absolute;
  top: 10px;
  right: 12px;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: none;
  background: #fff;
  color: #888;
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 2;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  transition: all 0.2s;
}

.featured-wishlist-btn:hover {
  color: #ff5722;
  transform: scale(1.1);
}

/* Image */
.featured-product-image-link {
  display: block;
  text-decoration: none;
}

.featured-product-image {
  width: 100%;
  aspect-ratio: 1 / 1;
  overflow: hidden;
  background: #f8f8f8;
}

.featured-product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition-duration: var(--animation-timing-300);
  transition-timing-function: var(--ease-out-quart);
  transition-property: opacity, transform;
}

.featured-product-box:hover .featured-product-image img {
  transform: scale(1.06);
}

/* Info */
.featured-product-info {
  padding: 14px 4px 0;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.featured-product-collection {
  font-size: 10px;
  font-weight: 700;
  color: #aaa;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin: 0 0 4px;
}

.featured-product-name-link {
  text-decoration: none;
}

.featured-product-name {
  font-size: 14px;
  font-weight: 800;
  color: #111;
  margin: 0 0 6px;
  line-height: 1.3;
  font-family: 'Public Sans', sans-serif;
  transition: color 0.2s;
}

.featured-product-name-link:hover .featured-product-name {
  color: #198754;
}

/* Stars */
.featured-product-stars {
  display: flex;
  gap: 2px;
  margin-bottom: 8px;
}

.featured-product-stars i {
  font-size: 12px;
  color: #ffc107;
}

/* Price */
.featured-product-price-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 12px;
}

.featured-product-price {
  font-size: 14px;
  font-weight: 700;
  color: #111;
}

.featured-product-old-price {
  font-size: 12px;
  color: #bbb;
  text-decoration: line-through;
}

/* Add to Cart */
.featured-atc-btn {
  width: 100%;
  padding: 10px;
  background: transparent;
  border: 1.5px solid #198754;
  color: #198754;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  cursor: pointer;
  border-radius: 0;
  font-family: 'Public Sans', sans-serif;
  transition: all 0.25s;
  margin-top: auto;
}

.featured-atc-btn:hover {
  background: #ff9505;
  border-color: #ff9505;
  color: #fff;
}

.featured-atc-btn:disabled {
  background: #f0f0f0;
  border-color: #ddd;
  color: #aaa;
  cursor: not-allowed;
}

/* View All */
.featured-view-all-wrap {
  display: flex;
  justify-content: center;
}

.featured-view-all-btn {
  display: inline-block;
  padding: 13px 40px;
  background: transparent;
  border: 2px solid #198754;
  color: #198754;
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  text-decoration: none;
  transition: all 0.25s;
}

.featured-view-all-btn:hover {
  background: #ff9505;
  border-color: #ff9505;
  color: #fff;
}

/* Responsive */
@media (max-width: 991px) {
  .featured-product-list {
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
  }

  .featured-products-title {
    font-size: 36px;
  }
}

@media (max-width: 575px) {
  .featured-products-inner {
    padding: 0 16px;
  }

  .featured-product-list {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }

  .featured-products-title {
    font-size: 28px;
    margin-bottom: 28px;
  }
}

/* ===== Marathon Countdown Section ===== */
.marathon-section {
  width: 100vw;
  position: relative;
  left: 50%;
  margin-left: -50vw;
  min-height: 680px;
  background-image: url('/img/img-marathon.png');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  overflow: hidden;
  z-index: 1;
}

/* Dark overlay */
.marathon-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.58);
  z-index: 1;
}

.marathon-content {
  position: relative;
  z-index: 2;
  max-width: 780px;
  padding: 80px 40px;
  margin: 0 auto;
}

.marathon-title {
  font-size: 56px;
  font-weight: 900;
  color: #fff;
  line-height: 1.15;
  margin: 0 0 28px;
  font-family: 'Public Sans', sans-serif;
}

.marathon-highlight {
  color: #f5a623;
  font-style: italic;
}

.marathon-desc {
  font-size: 15px;
  color: rgba(255, 255, 255, 0.8);
  line-height: 1.7;
  margin: 0 0 40px;
  max-width: 580px;
  margin-left: auto;
  margin-right: auto;
}

/* Countdown */
.countdown-wrap {
  display: flex;
  justify-content: center;
  gap: 12px;
  margin-bottom: 28px;
}

.countdown-box {
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(4px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  padding: 20px 28px;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 110px;
}

.countdown-num {
  font-size: 52px;
  font-weight: 900;
  color: #fff;
  line-height: 1;
  font-family: 'Public Sans', sans-serif;
  letter-spacing: -1px;
}

.countdown-label {
  font-size: 13px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.75);
  text-transform: capitalize;
  margin-top: 6px;
  letter-spacing: 0.5px;
}

.marathon-note {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.55);
  line-height: 1.6;
  margin: 0 0 32px;
  max-width: 480px;
  margin-left: auto;
  margin-right: auto;
}

.marathon-btn {
  display: inline-block;
  background: #f5a623;
  color: #fff;
  padding: 16px 40px;
  font-size: 13px;
  font-weight: 800;
  letter-spacing: 2px;
  text-transform: uppercase;
  text-decoration: none;
  transition: all 0.3s;
  border: none;
}

.marathon-btn:hover {
  background: #e09515;
  color: #fff;
  transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
  .marathon-section {
    min-height: auto;
  }

  .marathon-title {
    font-size: 34px;
  }

  .marathon-content {
    padding: 60px 24px;
  }

  .countdown-box {
    min-width: 72px;
    padding: 14px 16px;
  }

  .countdown-num {
    font-size: 36px;
  }

  .countdown-label {
    font-size: 11px;
  }
}

/* ===== What's Happening Section ===== */
.events-happening-section {
  width: 100vw;
  position: relative;
  left: 50%;
  margin-left: -50vw;
  background: #fff;
  padding: 60px 0 70px;
  z-index: 1;
}

.events-happening-inner {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 60px;
}

/* Header */
.events-happening-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 36px;
}

.events-happening-title {
  font-size: 40px;
  font-weight: 900;
  color: #535353;
  margin: 0;
  font-family: 'Public Sans', sans-serif;
}

.events-happening-tabs {
  display: flex;
  gap: 28px;
  align-items: center;
}

.events-tab-btn {
  background: none;
  border: none;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.5px;
  color: #aaa;
  cursor: pointer;
  padding: 0;
  font-family: 'Public Sans', sans-serif;
  transition: color 0.2s;
  text-transform: uppercase;
}

.events-tab-btn.active {
  color: #1a5c3a;
}

.events-tab-btn:hover {
  color: #1a5c3a;
}

/* Scroll Container with Arrows */
.events-scroll-container {
  position: relative;
  display: flex;
  align-items: center;
  gap: 0;
}

.events-arrow-btn {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  border: 2px solid #e0e0e0;
  background: #fff;
  color: #333;
  font-size: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  flex-shrink: 0;
  transition: all 0.25s;
  z-index: 2;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.events-arrow-btn:hover {
  background: #e84b0f;
  border-color: #e84b0f;
  color: #fff;
  box-shadow: 0 4px 14px rgba(232, 75, 15, 0.3);
}

.events-arrow-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.events-arrow-btn:disabled:hover {
  background: #fff;
  border-color: #e0e0e0;
  color: #333;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

/* Scroll Wrap */
.events-scroll-wrap {
  overflow-x: hidden;
  flex: 1;
  margin: 0 16px;
}

.events-scroll-track {
  display: flex;
  gap: 24px;
  transition: transform 0.4s ease;
}

/* Event Card */
.event-card {
  min-width: 340px;
  max-width: 340px;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
}

/* Image with hover overlay */
.event-card-image {
  width: 100%;
  aspect-ratio: 16 / 9;
  overflow: hidden;
  margin-bottom: 20px;
  position: relative;
  border-radius: 2px;
}

.event-img-placeholder {
  width: 100%;
  height: 100%;
  background: #ccc;
  transition: transform 0.4s ease;
}

.event-card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

/* Hover Overlay */
.event-card-hover-overlay {
  position: absolute;
  inset: 0;
  background: rgba(232, 75, 15, 0.88);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.35s ease;
}

.event-card:hover .event-card-hover-overlay {
  opacity: 1;
}

.event-card:hover .event-img-placeholder,
.event-card:hover .event-card-image img {
  transform: scale(1.05);
}

.event-card:hover .event-card-title {
  color: #e84b0f;
}

.event-hover-actions {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

.event-hover-btn {
  display: inline-block;
  padding: 10px 28px;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  text-decoration: none;
  transition: all 0.25s;
  font-family: 'Public Sans', sans-serif;
  background: #fff;
  color: #e84b0f;
  min-width: 180px;
  text-align: center;
}

.event-hover-btn:hover {
  background: #f5a623;
  color: #fff;
}

.event-hover-btn-outline {
  background: transparent;
  color: #fff;
}

.event-hover-btn-outline:hover {
  background: #f5a623;
  color: #fff;
}

/* Tags */
.event-tags {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 14px;
}

.event-tag {
  background: #e84b0f;
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  padding: 5px 12px;
  border-radius: 20px;
  letter-spacing: 0.3px;
  font-family: 'Public Sans', sans-serif;
}

/* Title */
.event-card-title {
  font-size: 20px;
  font-weight: 800;
  color: #111;
  margin: 0 0 16px;
  line-height: 1.3;
  font-family: 'Public Sans', sans-serif;
  transition: color 0.25s;
}

/* Meta */
.event-card-meta {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 20px;
}

.event-meta-row {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 13px;
  color: #555;
}

.event-meta-icon {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: rgba(60, 133, 153, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.event-meta-icon i {
  font-size: 15px;
  color: rgb(60, 133, 153);
}

/* Actions */
.event-card-actions {
  display: flex;
  gap: 24px;
  padding-top: 4px;
}

.event-action-link {
  font-size: 12px;
  font-weight: 700;
  color: #1a5c3a;
  text-decoration: none;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  transition: opacity 0.2s;
}

.event-action-link:hover {
  opacity: 0.7;
  color: #1a5c3a;
}

/* View All */
.events-view-all-wrap {
  display: flex;
  justify-content: center;
  margin-top: 48px;
}

.events-view-all-btn {
  display: inline-block;
  background: #1a5c3a;
  color: #fff;
  padding: 16px 40px;
  font-size: 13px;
  font-weight: 800;
  letter-spacing: 2px;
  text-transform: uppercase;
  text-decoration: none;
  transition: all 0.3s;
}

.events-view-all-btn:hover {
  background: #144d30;
  color: #fff;
  transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
  .events-happening-inner {
    padding: 0 16px;
  }

  .events-happening-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }

  .events-happening-title {
    font-size: 28px;
  }

  .event-card {
    min-width: 280px;
    max-width: 280px;
  }

  .event-card-title {
    font-size: 16px;
  }

  .events-arrow-btn {
    width: 36px;
    height: 36px;
    font-size: 18px;
  }
}

/* ===== Driven by Quality Section ===== */
.quality-section {
  width: 100vw;
  position: relative;
  left: 50%;
  margin-left: -50vw;
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 640px;
  z-index: 1;
}

/* Left image side */
.quality-image {
  position: relative;
  overflow: hidden;
  width: 100%;
  height: 100%;
  min-height: 640px;
}

.quality-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  position: absolute;
  top: 0;
  left: 0;
}

/* Right content side */
.quality-content {
  background: #f5a623;
  padding: 80px 70px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.quality-title {
  font-size: 42px;
  font-weight: 900;
  color: #111;
  line-height: 1.2;
  margin: 0 0 28px;
  font-family: 'Public Sans', sans-serif;
}

.quality-desc {
  font-size: 14px;
  color: #1a1a1a;
  line-height: 1.75;
  margin: 0 0 20px;
  max-width: 560px;
}

.quality-subdesc {
  font-size: 14px;
  color: #1a1a1a;
  line-height: 1.6;
  margin: 0 0 28px;
}

.quality-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.quality-list li {
  display: flex;
  align-items: center;
  gap: 14px;
  font-size: 14px;
  font-weight: 600;
  color: #111;
  font-family: 'Public Sans', sans-serif;
}

.quality-list li i {
  font-size: 22px;
  color: #111;
  flex-shrink: 0;
}

/* Responsive */
@media (max-width: 991px) {
  .quality-section {
    grid-template-columns: 1fr;
  }

  .quality-image {
    min-height: 360px;
  }

  .quality-content {
    padding: 50px 40px;
  }

  .quality-title {
    font-size: 32px;
  }
}

@media (max-width: 768px) {
  .quality-content {
    padding: 40px 24px;
  }

  .quality-title {
    font-size: 26px;
  }
}

/* ===== Run Wild Section ===== */
.runwild-section {
  width: 100vw;
  position: relative;
  left: 50%;
  margin-left: -50vw;
  background: #fff;
  padding: 80px 60px 80px;
}

.runwild-title {
  font-size: 50px;
  font-weight: 900;
  color: #1a1a1a;
  text-align: center;
  margin: 0 0 48px;
  font-family: 'Public Sans', sans-serif;
}

.runwild-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  max-width: 1300px;
  margin: 0 auto;
}

.runwild-card {
  display: flex;
  flex-direction: column;
  background: #e8450a;
  position: relative;
}

.photo-wrap {
  position: relative;
  flex: 1;
  line-height: 0;
}

.photo-wrap .photo {
  width: 100%;
  height: 320px;
  object-fit: cover;
  display: block;
}

/* Torn paper images */
.tear-img {
  display: block;
  width: 100%;
  object-fit: fill;
  position: relative;
  z-index: 2;
  filter: drop-shadow(0 0 6px white) drop-shadow(0 0 3px white);
}

.tear-img--top {
  margin-bottom: -28px;
}

.tear-img--bottom {
  margin-top: -28px;
}
/* Responsive */
@media (max-width: 991px) {
  .runwild-section {
    padding: 50px 40px 60px;
  }
  .runwild-title {
    font-size: 32px;
  }
  .runwild-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 575px) {
  .runwild-section {
    padding: 40px 16px 50px;
  }
  .runwild-title {
    font-size: 26px;
    margin-bottom: 28px;
  }
  .runwild-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  .tear-img--top {
    margin-bottom: -20px;
  }
  .tear-img--bottom {
    margin-top: -20px;
  }
}

/* ===== Cart Bottom Bar - product-cart-box ===== */
.product-cart-box {
  position: fixed;
  bottom: 60px;
  left: 0;
  right: 0;
  background: #f39c12;
  box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
  z-index: 998;
  padding: 14px 0;
  border-top: 1px solid #f0f0f0;
}

.cart-bar-inner {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cart-item-count {
  font-size: 12px;
  color: black(var(--grocery-content));
  margin: 0;
  font-weight: 500;
  padding-left: 50px;
  position: relative;
}

.cart-bar-title {
  font-size: 16px;
  font-weight: 800;
  color: rgb(var(--grocery-title));
  margin: 0;
  font-family: 'Public Sans', sans-serif;
  padding-left: 50px;
  position: relative;
}

.cart-bar-btn {
  padding: 10px 22px;
  padding-right: 80px;
}

.cart-bar-btn i {
  font-size: 16px;
}

/* ===== Modal - Grocery Styling ===== */
.grocery-modal,
.grocery-modal-content {
  border: none;
  border-radius: 22px;
  overflow: hidden;
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.12);
  background: #fff;
  font-family: 'Public Sans', sans-serif;
}

.grocery-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 22px;
  border-bottom: 1px solid #f0f0f0;
}

.grocery-modal-title {
  font-size: 18px;
  font-weight: 800;
  color: rgb(var(--grocery-title));
  margin: 0;
  font-family: 'poppins', 'Public Sans', sans-serif;
}

.grocery-modal-body {
  padding: 18px 22px;
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
  flex-shrink: 0;
  border: 1px solid #f0f0f0;
  background: #f8f8f8;
}

.modal-product-info p {
  font-size: 13px;
  color: rgb(var(--grocery-content));
  margin: 0 0 8px;
  line-height: 1.45;
  font-family: poppins, 'Public Sans', sans-serif;
}

.modal-product-price {
  font-size: 18px;
  font-weight: 800;
  color: #ff9505;
  margin: 0;
}

.old-price {
  display: block;
  margin-top: 8px;
  font-size: 13px;
  color: #999;
}

.qty-section-title {
  padding: 12px 0 10px;
  border-top: 1px solid #f0f0f0;
}

.qty-section-title h5 {
  font-size: 14px;
  font-weight: 700;
  color: rgb(var(--grocery-title));
  margin: 0;
}

.qty-selector {
  padding-bottom: 10px;
}

.qty-box .input-group {
  display: flex;
  align-items: center;
  background: #f7f7f7;
  border-radius: 18px;
  overflow: hidden;
  width: fit-content;
}

.qty-btn {
  width: 44px;
  height: 44px;
  border: none;
  background: #fff;
  color: #111;
  font-size: 20px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.qty-btn:hover:not(:disabled) {
  background: #f0f0f0;
}

.qty-btn:disabled {
  color: #ccc;
}

.qty-input {
  width: 64px;
  height: 44px;
  border: none;
  background: none;
  text-align: center;
  font-weight: 700;
  font-size: 16px;
  color: #111;
  outline: none;
  padding: 0;
}

.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.grocery-modal-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 14px;
  padding: 18px 22px;
  background: #fafafa;
  border-top: 1px solid #f0f0f0;
}

.modal-footer-info h5 {
  font-size: 12px;
  color: #000000;
  margin: 0;
  font-weight: 500;
}

.modal-footer-info h4 {
  font-size: 18px;
  font-weight: 800;
  color: #ff9505;
  margin: 0;
}

.cart-bar-btn {
  min-width: 150px;
  padding: 12px 16px;
  border-radius: 2px;
  border: none;
  background: #ff9505;
  color: #fff;
  font-size: 13px;
  font-weight: 800;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  cursor: pointer;
}

.cart-bar-btn:hover:not(:disabled) {
  background: #005523;
}

.cart-bar-btn:disabled {
  opacity: 0.75;
  cursor: not-allowed;
}

.btn-cancel,
.btn-confirm {
  min-width: 150px;
  height: 44px;
  border-radius: 14px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
}

.btn-cancel {
  border: 1.5px solid #e0e0e0;
  background: #fff;
  color: #111;
}

.btn-cancel:hover {
  background: #f7f7f7;
}

.btn-confirm {
  border: none;
  background: #e84b0f;
  color: #fff;
}

.btn-confirm:hover:not(:disabled) {
  background: #d96f0d;
}

.btn-confirm:disabled {
  background: #ddd;
  cursor: not-allowed;
}

/* ===== Bottom Space ===== */
.grocery-bottom-space {
  height: 80px;
}

/* ===== Responsive ===== */
@media (min-width: 576px) {
  .product-offer-list {
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }
}

@media (min-width: 769px) {
  .grocery-search-section {
    margin-top: 16px;
  }

  .product-cart-box {
    bottom: 0;
  }
}

@media (min-width: 992px) {
  .product-offer-list {
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
  }

  .grocery-store-page {
    max-width: 1200px;
    margin: 0 auto;
  }

  .section-title {
    font-size: 22px;
  }

  .product-name {
    font-size: 15px;
  }

  .product-price {
    font-size: 17px;
  }

  .grocery-category-box .category-icon-wrap {
    width: 68px;
    height: 68px;
    border-radius: 18px;
  }

  .grocery-category-box .category-icon-wrap i {
    font-size: 28px;
  }

  .grocery-category-box h5 {
    font-size: 12px;
  }
}

@media (min-width: 1200px) {
  .product-offer-list {
    gap: 24px;
  }

  .product-content {
    padding: 14px;
  }
}

@media (max-width: 575px) {
  .product-offer-list {
    gap: 10px;
  }

  .product-content {
    padding: 10px;
  }

  .product-name {
    font-size: 13px;
  }

  .product-price {
    font-size: 14px;
  }

  .grocery-category-box .category-icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 14px;
  }

  .grocery-category-box .category-icon-wrap i {
    font-size: 22px;
  }

  .grocery-category-box h5 {
    font-size: 10px;
  }

  .modal-dialog {
    margin: 0;
    min-height: auto;
    display: flex;
    align-items: flex-end;
  }

  .grocery-modal-content {
    border-radius: 24px 24px 0 0;
    width: 100%;
  }
}

.product-box {
  width: 100%;
  position: relative;
  background: #fff;
  display: flex;
  flex-direction: column;
  transition:
    transform 0.25s,
    box-shadow 0.25s;
}

.product-box:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 28px rgba(0, 0, 0, 0.09);
}

.product-image-wrap {
  position: relative;
  margin-bottom: 12px;
  height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.3s ease;
  overflow: hidden;
}

.product-image-wrap img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition-duration: var(--animation-timing-300);
  transition-timing-function: var(--ease-out-quart);
  transition-property: opacity;
}

.product-image-wrap .img-fluid {
  transition: transform 0.3s ease;
}

.product-image-wrap:hover .img-fluid {
  transform: scale(1.05);
}

.product-badges {
  position: absolute;
  top: 10px;
  left: 0;
  border-radius: 0 50px 50px 0;
  z-index: 2;
}

.product-badges .badge {
  font-size: 0.625rem;
  font-weight: 700;
  padding: 5px 10px;
  letter-spacing: 0.5px;
  border-radius: 0 50px 50px 0;
}

.sale-badge {
  background-color: #ff5722 !important;
  color: #fff !important;
}

.new-badge {
  background-color: #1976d2 !important;
  color: #fff !important;
}

.wishlist-btn {
  position: absolute;
  top: 10px;
  right: 15px;
  background: #fff;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  color: #666;
  z-index: 2;
  transition: all 0.2s ease;
}

.wishlist-btn:hover {
  color: #ff5722;
  transform: scale(1.1);
}

.product-content {
  text-align: left;
  padding: 0;
}

.product-name-link {
  text-decoration: none;
  color: #111;
}

.product-name-link:hover .product-name {
  color: #198754;
}

.product-name {
  font-size: 14px;
  font-weight: 800;
  margin-bottom: 4px;
  font-family: 'Public Sans', sans-serif;
}

.product-subtitle {
  display: block;
}

.add-to-cart-outline-btn {
  text-transform: uppercase;
  border: 1px solid #198754 !important;
  color: #198754 !important;
  font-size: 11px !important;
  font-weight: 700 !important;
  padding: 10px !important;
  border-radius: 0 !important;
  background-color: transparent !important;
  transition: all 0.3s ease;
}

.add-to-cart-outline-btn:hover {
  background-color: #ff9505 !important;
  border: 1px solid #ff9505 !important;
  color: #fff !important;
}

/* Grid layout for products */
.product-offer-list {
  display: grid !important;
  grid-template-columns: repeat(4, 1fr) !important;
  gap: 24px;
  list-style: none;
  padding: 0;
  margin: 0;
}

@media (max-width: 991px) {
  .product-offer-list {
    grid-template-columns: repeat(2, 1fr) !important;
  }
}

@media (max-width: 575px) {
  .product-offer-list {
    grid-template-columns: 1fr !important;
  }
}
</style>
