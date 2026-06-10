`x`<template>
    <Head title="FAQ" />

    <div class="faq-page">
        <!-- Hero -->
        <section class="faq-hero">
            <div class="faq-hero-bg">
                <img src="/img/img-about1.png" alt="FAQ Hero" />
            </div>
            <div class="faq-hero-overlay"></div>
            <div class="faq-hero-content">
                <div class="breadcrumb-wrapper">
                    <Breadcrumb :items="breadcrumbItems" />
                </div>
                <h1 class="faq-hero-title">Frequently Asked Questions</h1>
            </div>
        </section>

        <!-- FAQ Content -->
        <section class="faq-content-section">
            <div class="faq-inner">
                <h2 class="faq-section-title">We're here to help</h2>
                <p class="faq-section-text">
                    Find answers to the most common questions about our products, shipping, returns, and more.
                </p>

                <div class="faq-accordion">
                    <div class="faq-item" v-for="(faq, index) in faqs" :key="index" :class="{ active: activeIndex === index }">
                        <div class="faq-question" @click="toggleFaq(index)">
                            <h3>{{ faq.question }}</h3>
                            <i class="ri-arrow-down-s-line icon-down" v-if="activeIndex !== index"></i>
                            <i class="ri-arrow-up-s-line icon-up" v-else></i>
                        </div>
                        <div class="faq-answer" v-show="activeIndex === index">
                            <p>{{ faq.answer }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/components/breadcrumb/layout-breadcrumb.vue';

// Breadcrumb items
const breadcrumbItems = [{ label: 'FAQ', link: '#' }];

const activeIndex = ref(null);

const toggleFaq = (index) => {
    activeIndex.value = activeIndex.value === index ? null : index;
};

const faqs = ref([
    {
        question: "What is your return policy?",
        answer: "We offer a 30-day return policy for unused and unworn items with their original tags attached. Please contact our support team to initiate a return."
    },
    {
        question: "How long does shipping take?",
        answer: "Standard shipping typically takes 3-5 business days within the Philippines. Delivery times may vary depending on your exact location."
    },
    {
        question: "Do you offer international shipping?",
        answer: "Currently, we only ship within the Philippines. We are working hard to expand our reach internationally in the near future."
    },
    {
        question: "How can I track my order?",
        answer: "Once your order has been shipped, you will receive an email with a tracking number and a link to monitor your package's progress."
    },
    {
        question: "Are your products suitable for extreme weather?",
        answer: "Yes, our gear is designed with the great outdoors in mind. We use durable, high-quality materials built to withstand rugged terrains and changing weather conditions."
    }
]);
</script>

<style scoped>
.faq-page {
    font-family: 'Public Sans', sans-serif;
    background: #fff;
    min-height: 100vh;
}

/* Breadcrumb Override */
.faq-hero-content :deep(.breadcrumb) {
    justify-content: center;
}
.faq-hero-content :deep(.breadcrumb-item),
.faq-hero-content :deep(.breadcrumb-item:not(:last-child)::after),
.faq-hero-content :deep(.breadcrumb-home),
.faq-hero-content :deep(.breadcrumb-item a),
.faq-hero-content :deep(.breadcrumb-item.active),
.faq-hero-content :deep(.breadcrumb-item span) {
    color: #ffffff;
}
.faq-hero-content :deep(.breadcrumb-home:hover),
.faq-hero-content :deep(.breadcrumb-item a:hover) {
    color: #ff9505;
    text-decoration: none;
}

/* Hero */
.faq-hero {
    position: relative;
    height: 400px;
    padding-top: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    overflow: hidden;
}

.faq-hero-bg {
    position: absolute;
    inset: 0;
}

.faq-hero-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.faq-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    z-index: 1;
}

.faq-hero-content {
    position: relative;
    z-index: 2;
    max-width: 600px;
    padding: 0 24px;
}

.faq-hero-title {
    font-size: 56px;
    font-weight: 800;
    text-align: center;
    color: #ffffff;
    letter-spacing: -1px;
    line-height: 1.2;
    font-family: 'Poppins', sans-serif;
    display: block;
    width: 100%;
}

/* Inner Container */
.faq-inner {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 24px;
}

/* FAQ Content */
.faq-content-section {
    padding: 80px 0;
    color: #555;
    font-family: 'Poppins', sans-serif;
}

.faq-section-title {
    font-size: 36px;
    font-weight: 800;
    text-align: center;
    margin-bottom: 20px;
    color: #535353;
    letter-spacing: -1px;
    font-family: 'Poppins', sans-serif;
}

.faq-section-text {
    font-size: 16px;
    color: #555;
    line-height: 1.7;
    text-align: center;
    max-width: 700px;
    margin: 0 auto 50px;
}

/* Accordion */
.faq-accordion {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.faq-item {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.faq-item.active {
    border-color: #ff9505;
    box-shadow: 0 4px 15px rgba(255, 149, 5, 0.15);
}

.faq-question {
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #fcfcfc;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.faq-question:hover {
    background-color: #f5f5f5;
}

.faq-item.active .faq-question {
    background-color: #fff;
}

.faq-question h3 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0;
    padding-right: 20px;
}

.faq-question i {
    font-size: 24px;
    color: #777;
}

.faq-item.active .faq-question h3,
.faq-item.active .faq-question i {
    color: #ff9505;
}

.faq-answer {
    padding: 0 24px 20px;
    background-color: #fff;
}

.faq-answer p {
    font-size: 15px;
    color: #666;
    line-height: 1.7;
    margin: 0;
    border-top: 1px solid #eee;
    padding-top: 15px;
}

/* Responsive */
@media (max-width: 768px) {
    .faq-hero-title {
        font-size: 40px;
        text-align: center;
    }
    
    .faq-hero {
        height: 300px;
    }
    
    .faq-section-title {
        font-size: 28px;
    }
    
    .faq-question h3 {
        font-size: 16px;
    }
}
</style>
