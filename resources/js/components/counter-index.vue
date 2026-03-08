<template>
    <div class="product-quantity border-0 bg-secondary-transparent">
        <span class="quantity-btn" @click="decrement"
            ><vue-feather
                type="minus-circle"
                class="feather-search"
            ></vue-feather
        ></span>
        <input
            type="text"
            class="quntity-input bg-transparent"
            v-model="quantity"
        />
        <span class="quantity-btn" @click="increment"
            >+<vue-feather type="plus-circle" class="plus-circle"></vue-feather
        ></span>
    </div>
</template>

<script>
export default {
    data() {
        return {
            quantity: 0,
        };
    },
    methods: {
        increment() {
            this.quantity += 1;
        },
        decrement() {
            if (this.quantity > 0) {
                this.quantity -= 1;
            }
        },
    },
};
</script>
