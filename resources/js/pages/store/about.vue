<template>
    <Head title="About Us" />

    <div class="about-page">
        <!-- Hero -->
        <section class="about-hero">
            <div class="about-hero-bg">
                <img src="/img/img-about1.png" alt="About Hero" />
            </div>
            <div class="about-hero-overlay"></div>
            <div class="about-hero-content">
                <div class="breadcrumb-wrapper">
                    <Breadcrumb :items="breadcrumbItems" />
                </div>
                <h1 class="about-hero-title">About Us</h1>
            </div>
        </section>

        <!-- About Intro -->
        <section class="about-intro">
            <div class="about-inner">
                <h2 class="about-intro-title">About Us Tribu Pakaras</h2>
                <p class="about-intro-text">
                    Tribu Pakaras Outdoor is more than a brand—it's a community
                    built for those who move with purpose, passion, and courage.
                    Inspired by the meaning of
                    <strong>"pakaras"</strong>—fearless and determined—we exist
                    for individuals who choose adventure without hesitation.
                </p>
                <p class="about-intro-text">
                    Rooted in the spirit of the Filipino outdoors, we bring
                    together athletes, explorers, and everyday adventurers who
                    share one mindset: to keep going, no matter the terrain.
                </p>
            </div>
        </section>

        <!-- Our Story -->
        <section class="about-story">
            <div class="about-inner about-story-grid">
                <div class="about-story-image">
                    <img src="/img/img-about2.png" alt="Our Story" />
                </div>
                <div class="about-story-content">
                    <h2 class="about-section-title">Our Story</h2>
                    <p class="about-section-text">
                        Born from the growing trail run and trail running
                        culture in the Philippines, Tribu Pakaras started as a
                        passion-driven movement supporting local athletes and
                        the outdoor community. From trail races to everyday
                        training, we built a space where people can connect,
                        grow, and push beyond limits.
                    </p>
                    <p class="about-section-text">
                        What began as a small initiative has evolved into a
                        trusted team for outdoor gear, performance athleti, and
                        community-driven experiences—serving adventurers across
                        the country.
                    </p>
                </div>
            </div>
        </section>

        <!-- What We Do -->
        <section class="about-whatwedo">
            <div class="about-inner about-story-grid reverse">
                <div class="about-story-content">
                    <h2 class="about-section-title">What We Do</h2>
                    <p class="about-section-text">
                        We provide curated outdoor and performance products
                        designed for real-world adventure—from trail running and
                        hiking to endurance sports. Whether you're preparing for
                        your first race or your next ultra stop, we keep you
                        with gear that supports your journey.
                    </p>
                    <p class="about-section-text">
                        Beyond products, we actively support the outdoor
                        brave—collaborating with events, athletes, and
                        organizations that promote passion, resilience, and
                        exploration.
                    </p>
                </div>
                <div class="about-story-image">
                    <img src="/img/img-about3.png" alt="What We Do" />
                </div>
            </div>
        </section>

        <!-- Mission & Vision -->
        <section class="about-mission-vision">
            <div class="about-mv-grid">
                <div class="about-mv-panel">
                    <div class="about-mv-bg">
                        <img src="/img/img-about4.png" alt="" />
                    </div>
                    <div class="about-mv-overlay"></div>
                    <div class="about-mv-content">
                        <h3 class="about-mv-title">Our Mission</h3>
                        <p class="about-mv-text">
                            To empower every Filipino adventurer to move with
                            puso (heart) and galing (excellence)—on the trail,
                            on the road, and in life.
                        </p>
                    </div>
                </div>
                <div class="about-mv-panel">
                    <div class="about-mv-bg">
                        <img src="/img/img-about5.png" alt="" />
                    </div>
                    <div class="about-mv-overlay"></div>
                    <div class="about-mv-content">
                        <h3 class="about-mv-title">Our Vision</h3>
                        <p class="about-mv-text">
                            To become a leading outdoor lifestyle brand in the
                            Philippines—building a strong, united tribe of
                            explorers who inspire others to embrace the outdoors
                            and live fearlessly.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Join the Tribe -->
        <section class="about-join">
            <div class="about-join-bg">
                <img src="/img/img-about6.png" alt="" />
            </div>
            <div class="about-join-overlay"></div>
            <div class="about-join-inner">
                <h2 class="about-join-title">Join the Tribe</h2>
                <p class="about-join-desc">
                    At Tribu Pakaras Outdoor, you're not just buying gear—you're
                    becoming part of a movement. A tribe that runs with heart,
                    climbs with purpose, and explores without limits.
                </p>
                <p class="about-join-tagline">Puso. Galing. Pakaras.</p>
            </div>
        </section>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/components/breadcrumb/layout-breadcrumb.vue';
import { computed } from 'vue';

// Breadcrumb items
const breadcrumbItems = [{ label: 'About Us', link: '#' }];
</script>

<style scoped>
.about-page {
    font-family: 'Public Sans', sans-serif;
    background: #fff;
    min-height: 100vh;
}

/* Breadcrumb Color Override for About Us Page */
.about-hero-content :deep(.breadcrumb) {
    justify-content: center;
}

.about-hero-content :deep(.breadcrumb-item) {
    color: #ffffff;
}

.about-hero-content :deep(.breadcrumb-item:not(:last-child)::after) {
    color: #ffffff;
}

.about-hero-content :deep(.breadcrumb-home) {
    color: #ffffff;
}

.about-hero-content :deep(.breadcrumb-home:hover) {
    color: #ff9505;
    text-decoration: none;
}

.about-hero-content :deep(.breadcrumb-item a) {
    color: #ffffff;
}

.about-hero-content :deep(.breadcrumb-item a:hover) {
    color: #ff9505;
    text-decoration: none;
}

.about-hero-content :deep(.breadcrumb-item.active) {
    color: #ffffff;
}

.about-hero-content :deep(.breadcrumb-item span) {
    color: #ffffff;
}

/* Hero */
.about-hero {
    position: relative;
    height: 450px;
    padding-top: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    overflow: hidden;
}

.about-hero-bg {
    position: absolute;
    inset: 0;
}

.about-hero-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.about-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    z-index: 1;
}

.about-hero-content {
    position: relative;
    z-index: 2;
    max-width: 600px;
    padding: 0 24px;
}

.about-hero-title {
    font-size: 64px;
    font-weight: 800;
    text-align: left;
    color: #ffffff;
    letter-spacing: -1px;
    line-height: 1.2;
    font-family: 'Poppins', sans-serif;
    display: block;
    width: 100%;
}

/* Inner Container */
.about-inner {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 24px;
}

/* About Intro */
.about-intro {
    padding: 70px 0 40px;
    text-align: center;
}

.about-intro-title {
    font-size: 40px;
    font-weight: 800;
    text-align: center;
    margin-bottom: 40px;
    color: #535353;
    letter-spacing: -1px;
    line-height: 1.2;
    font-family: 'Poppins', sans-serif;
    display: block;
    width: 100%;
}

.about-intro-text {
    font-size: 15px;
    color: #555;
    line-height: 1.7;
    max-width: 800px;
    font-family: 'Poppins', sans-serif;
    margin: 0 auto 20px;
}

/* About Story & What We Do Sections */
.about-story,
.about-whatwedo {
    padding: 60px 0;
    color: #555;
    font-family: 'Poppins', sans-serif;
}

.about-story-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    align-items: center;
}

.about-story-grid.reverse {
    direction: rtl;
}

.about-story-grid.reverse .about-story-content {
    direction: ltr;
}

.about-story-image {
    width: 100%;
    overflow: hidden;
    border-radius: 5px;
}

.about-story-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.4s ease;
}

.about-story-image:hover img {
    transform: scale(1.04);
}

.about-section-title {
    font-size: 28px;
    font-weight: 800;
    color: #535353;
    margin: 0 0 20px;
    font-family: 'Public Sans', sans-serif;
}

.about-section-text {
    font-size: 14px;
    color: #555;
    line-height: 1.7;
    margin: 0 0 20px;
}

/* Mission & Vision */
.about-mission-vision {
    padding: 0;
}

.about-mv-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: 420px;
    gap: 8px;
    background: #fff;
}

.about-mv-panel {
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.about-mv-bg {
    position: absolute;
    inset: 0;
    background: #2a4a2a;
}

.about-mv-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.about-mv-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1;
}

.about-mv-content {
    position: relative;
    z-index: 2;
    padding: 60px 70px;
    text-align: center;
}

.about-mv-title {
    font-size: 36px;
    font-weight: 900;
    color: #fff;
    margin: 0 0 24px;
    font-family: 'Public Sans', sans-serif;
}

.about-mv-text {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.85);
    line-height: 1.8;
    margin: 0;
    max-width: 420px;
    margin-left: auto;
    margin-right: auto;
    text-align: center;
}

/* Join the Tribe */
.about-join {
    position: relative;
    padding: 100px 24px;
    text-align: center;
    overflow: hidden;
    margin-top: 8px;
}

.about-join-bg {
    position: absolute;
    inset: 0;
    background: #c0440a;
    /* Replace background color with your image */
}

.about-join-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.about-join-overlay {
    position: absolute;
    inset: 0;
    background: rgba(200, 65, 15, 0.82);
    z-index: 1;
}

.about-join-inner {
    position: relative;
    z-index: 2;
    max-width: 700px;
    margin: 0 auto;
}

.about-join-title {
    font-size: 42px;
    font-weight: 900;
    color: #fff;
    margin: 0 0 20px;
    font-family: 'Public Sans', sans-serif;
}

.about-join-desc {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.85);
    line-height: 1.8;
    margin: 0 0 24px;
}

.about-join-tagline {
    font-size: 16px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.75);
    margin: 0;
    letter-spacing: 0.5px;
}

/* Responsive */
@media (max-width: 768px) {
    .about-mv-grid {
        grid-template-columns: 1fr;
    }

    .about-mv-content {
        padding: 50px 24px;
    }

    .about-mv-title {
        font-size: 28px;
    }

    .about-join-title {
        font-size: 30px;
    }

    .about-join {
        padding: 70px 24px;
    }
}
</style>
