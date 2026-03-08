<template>
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">{{ title }}</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <Link href="/dashboard/">{{ text }}</Link>
                    </li>
                    <li class="breadcrumb-item active">{{ text1 }}</li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script>
import { Link } from '@inertiajs/vue3';

export default {
    props: {
        title: {
            type: String,
            default: '',
        },
        text: {
            type: String,
            default: '',
        },
        text1: {
            type: String,
            default: '',
        },
    },
};
</script>
