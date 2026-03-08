<template>
    <Head title="Settings" />

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Profile Settings</h4>
                <h6>Update your brand partner profile</h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <form @submit.prevent="submitProfile">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required"
                                    >Brand Name</label
                                >
                                <input
                                    v-model="profileForm.name"
                                    type="text"
                                    class="form-control"
                                />
                                <input-error
                                    :message="profileForm.errors.name"
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Person</label>
                                <input
                                    v-model="profileForm.contact_person"
                                    type="text"
                                    class="form-control"
                                />
                                <input-error
                                    :message="profileForm.errors.contact_person"
                                />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input
                                    v-model="profileForm.phone"
                                    type="text"
                                    class="form-control"
                                />
                                <input-error
                                    :message="profileForm.errors.phone"
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input
                                    :value="brandPartner.email"
                                    type="email"
                                    class="form-control"
                                    disabled
                                />
                                <small class="text-muted"
                                    >Email cannot be changed</small
                                >
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea
                                v-model="profileForm.address"
                                class="form-control"
                                rows="2"
                            ></textarea>
                            <input-error
                                :message="profileForm.errors.address"
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea
                                v-model="profileForm.description"
                                class="form-control"
                                rows="3"
                            ></textarea>
                            <input-error
                                :message="profileForm.errors.description"
                            />
                        </div>
                        <submit-btn :loading="profileForm.processing">
                            Update Profile
                        </submit-btn>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <form @submit.prevent="submitPassword">
                        <div class="mb-3">
                            <label class="form-label required"
                                >Current Password</label
                            >
                            <input
                                v-model="passwordForm.current_password"
                                type="password"
                                class="form-control"
                            />
                            <input-error
                                :message="passwordForm.errors.current_password"
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label required"
                                >New Password</label
                            >
                            <input
                                v-model="passwordForm.password"
                                type="password"
                                class="form-control"
                            />
                            <input-error
                                :message="passwordForm.errors.password"
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label required"
                                >Confirm New Password</label
                            >
                            <input
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                class="form-control"
                            />
                        </div>
                        <submit-btn :loading="passwordForm.processing">
                            Update Password
                        </submit-btn>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Logo</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img
                            :src="brandPartner.logo_url || '/img/default.png'"
                            alt="Logo"
                            class="img-fluid rounded"
                            style="max-width: 200px"
                        />
                    </div>
                    <form @submit.prevent="submitLogo">
                        <div class="mb-3">
                            <input
                                type="file"
                                class="form-control"
                                accept="image/*"
                                @change="onLogoChange"
                            />
                            <input-error :message="logoForm.errors.logo" />
                        </div>
                        <submit-btn
                            :loading="logoForm.processing"
                            :disabled="!logoForm.logo"
                        >
                            Upload Logo
                        </submit-btn>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Store URL</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">Your public store page:</p>
                    <a :href="storeUrl" target="_blank" class="text-primary">
                        {{ storeUrl }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    brandPartner: Object,
});

const storeUrl = computed(() => {
    return `${window.location.origin}/${props.brandPartner.slug}`;
});

const profileForm = useForm({
    name: props.brandPartner.name,
    contact_person: props.brandPartner.contact_person || '',
    phone: props.brandPartner.phone || '',
    address: props.brandPartner.address || '',
    description: props.brandPartner.description || '',
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const logoForm = useForm({
    logo: null,
});

const submitProfile = () => {
    profileForm.put(route('brand-partner.settings.update'));
};

const submitPassword = () => {
    passwordForm.put(route('brand-partner.settings.password'), {
        onSuccess: () => passwordForm.reset(),
    });
};

const onLogoChange = (event) => {
    logoForm.logo = event.target.files[0];
};

const submitLogo = () => {
    logoForm.post(route('brand-partner.settings.logo'), {
        onSuccess: () => {
            logoForm.reset();
        },
    });
};
</script>
