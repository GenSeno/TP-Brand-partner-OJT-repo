<template>
    <!-- <Head title="Home" /> -->

    <layout-header></layout-header>
    <layout-sidebar></layout-sidebar>
    <div class="page-wrapper pagehead">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Blank Page</h4>
                    <h6>Sub Title</h6>
                </div>
                <ul class="table-top-head">
                    <li>
                        <a
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Refresh"
                            ><i class="ti ti-refresh"></i
                        ></a>
                    </li>
                    <li>
                        <a
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Collapse"
                            id="collapse-header"
                            @click="toggleHeader"
                            ><i class="ti ti-chevron-up"></i
                        ></a>
                    </li>
                </ul>
            </div>
        </div>
        <div
            class="footer d-sm-flex align-items-center justify-content-between border-top bg-white p-3 align-content-end"
        >
            <p class="mb-0">
                {{ new Date().getFullYear() }} © DreamsPOS. All Right Reserved
            </p>
            <p>
                Designed &amp; Developed By
                <a href="javascript:void(0);" class="text-primary">Dreams</a>
            </p>
        </div>
    </div>
</template>
<script setup>
function toggleHeader() {
    const el = document.getElementById('collapse-header');
    if (el) el.classList.toggle('active');
    document.body.classList.toggle('header-collapse');
}
</script>
