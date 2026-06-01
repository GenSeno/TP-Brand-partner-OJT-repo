<template>
    <div>
        <h2 class="account-page-title">My Profile</h2>
        <p class="account-page-subtitle">Manage and protect your account</p>

        <!-- My Details -->
        <div class="account-section">
            <div class="section-header">
                <h4>My Details</h4>
                <button class="edit-btn" @click="openEditModal">EDIT</button>
            </div>

            <div class="detail-field">
                <label>FIRST NAME</label>
                <div class="detail-value">{{ firstName }}</div>
            </div>

            <div class="detail-field">
                <label>LAST NAME</label>
                <div class="detail-value">{{ lastName }}</div>
            </div>

            <div class="detail-field">
                <label>DATE OF BIRTH</label>
                <div class="detail-value">{{ user.date_of_birth || '—' }}</div>
            </div>

            <div class="detail-field">
                <label>GENDER</label>
                <div class="detail-value">{{ user.gender || '—' }}</div>
            </div>
        </div>

        <!-- Login Details -->
        <div class="account-section" style="margin-top: 32px;">
            <div class="section-header">
                <h4>Login Details</h4>
            </div>

            <div class="detail-field login-field">
                <div class="login-field-inner">
                    <label>EMAIL ADDRESS</label>
                    <div class="detail-value">{{ user.email }}</div>
                </div>
                <button class="edit-btn" @click="openEmailModal">EDIT</button>
            </div>

            <div class="detail-field login-field">
                <div class="login-field-inner">
                    <label>PASSWORD</label>
                    <div class="detail-value">****************</div>
                </div>
                <button class="edit-btn" @click="openPasswordModal">EDIT</button>
            </div>
        </div>

        <!-- Edit Details Modal -->
        <Teleport to="body">
            <div v-if="showEditModal" class="profile-modal-overlay" @click.self="closeAllModals">
                <div class="profile-modal">
                    <button class="profile-modal-close" @click="closeAllModals">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                            <path d="M1 1L17 17M17 1L1 17" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <h3 class="profile-modal-title">Edit my Details</h3>

                    <div class="profile-modal-field">
                        <label>FIRST NAME</label>
                        <input v-model="form.firstName" type="text" class="profile-modal-input" />
                    </div>
                    <div class="profile-modal-field">
                        <label>LAST NAME</label>
                        <input v-model="form.lastName" type="text" class="profile-modal-input" />
                    </div>
                    <div class="profile-modal-field">
                        <label>DATE OF BIRTH</label>
                        <div class="profile-dob-row">
                            <div class="profile-dob-group">
                                <span class="profile-dob-label">DD</span>
                                <input v-model="form.dobDay" type="text" maxlength="2" class="profile-modal-input profile-dob-input" placeholder="DD" />
                            </div>
                            <div class="profile-dob-group">
                                <span class="profile-dob-label">MM</span>
                                <input v-model="form.dobMonth" type="text" maxlength="2" class="profile-modal-input profile-dob-input" placeholder="MM" />
                            </div>
                            <div class="profile-dob-group">
                                <span class="profile-dob-label">YYYY</span>
                                <input v-model="form.dobYear" type="text" maxlength="4" class="profile-modal-input profile-dob-input" placeholder="YYYY" />
                            </div>
                        </div>
                    </div>
                    <div class="profile-modal-field">
                        <label>GENDER</label>
                        <div class="profile-gender-row">
                            <label class="profile-gender-option" v-for="g in ['Male', 'Female', 'Other']" :key="g">
                                <input type="radio" :value="g" v-model="form.gender" class="profile-gender-radio" />
                                <span class="profile-gender-label">{{ g }}</span>
                            </label>
                        </div>
                    </div>

                    <button class="profile-btn-primary" @click="saveDetails">UPDATE DETAILS</button>
                    <button class="profile-btn-secondary" @click="closeAllModals">CANCEL</button>
                </div>
            </div>
        </Teleport>

        <!-- Edit Email Modal -->
        <Teleport to="body">
            <div v-if="showEmailModal" class="profile-modal-overlay" @click.self="closeAllModals">
                <div class="profile-modal">
                    <button class="profile-modal-close" @click="closeAllModals">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                            <path d="M1 1L17 17M17 1L1 17" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <h3 class="profile-modal-title">Edit your email</h3>

                    <div class="profile-modal-field">
                        <label>EMAIL ADDRESS</label>
                        <input v-model="emailForm.email" type="email" class="profile-modal-input" />
                    </div>

                    <button class="profile-btn-primary" @click="saveEmail">SAVE CHANGES</button>
                    <button class="profile-btn-secondary" @click="closeAllModals">CANCEL</button>
                </div>
            </div>
        </Teleport>

        <!-- Edit Password Modal -->
        <Teleport to="body">
            <div v-if="showPasswordModal" class="profile-modal-overlay" @click.self="closeAllModals">
                <div class="profile-modal">
                    <button class="profile-modal-close" @click="closeAllModals">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                            <path d="M1 1L17 17M17 1L1 17" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <h3 class="profile-modal-title">Edit Password</h3>

                    <div class="profile-modal-field">
                        <label>OLD PASSWORD</label>
                        <input v-model="passwordForm.oldPassword" type="password" class="profile-modal-input" />
                    </div>
                    <div class="profile-modal-field">
                        <label>NEW PASSWORD</label>
                        <input v-model="passwordForm.newPassword" type="password" class="profile-modal-input" />
                    </div>
                    <div class="profile-modal-field">
                        <label>CONFIRM NEW PASSWORD</label>
                        <input v-model="passwordForm.confirmPassword" type="password" class="profile-modal-input" />
                    </div>

                    <button class="profile-btn-primary" @click="savePassword">SAVE CHANGES</button>
                    <button class="profile-btn-secondary" @click="closeAllModals">CANCEL</button>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update-details', 'update-email', 'update-password']);

const showEditModal = ref(false);
const showEmailModal = ref(false);
const showPasswordModal = ref(false);

const firstName = computed(() => (props.user.name || '').split(' ')[0] || '');
const lastName = computed(() => (props.user.name || '').split(' ').slice(1).join(' ') || '');

const form = ref({ firstName: '', lastName: '', dobDay: '', dobMonth: '', dobYear: '', gender: '' });
const emailForm = ref({ email: '' });
const passwordForm = ref({ oldPassword: '', newPassword: '', confirmPassword: '' });

function openEditModal() {
    const names = (props.user.name || '').split(' ');
    form.value.firstName = names[0] || '';
    form.value.lastName = names.slice(1).join(' ') || '';
    form.value.gender = props.user.gender || '';

    if (props.user.date_of_birth) {
        const dob = new Date(props.user.date_of_birth);
        form.value.dobDay = String(dob.getDate()).padStart(2, '0');
        form.value.dobMonth = String(dob.getMonth() + 1).padStart(2, '0');
        form.value.dobYear = String(dob.getFullYear());
    } else {
        form.value.dobDay = '';
        form.value.dobMonth = '';
        form.value.dobYear = '';
    }

    showEditModal.value = true;
}

function openEmailModal() {
    emailForm.value.email = props.user.email || '';
    showEmailModal.value = true;
}

function openPasswordModal() {
    passwordForm.value = { oldPassword: '', newPassword: '', confirmPassword: '' };
    showPasswordModal.value = true;
}

function closeAllModals() {
    showEditModal.value = false;
    showEmailModal.value = false;
    showPasswordModal.value = false;
}

function saveDetails() {
    emit('update-details', { ...form.value });
    closeAllModals();
}

function saveEmail() {
    emit('update-email', { email: emailForm.value.email });
    closeAllModals();
}

function savePassword() {
    emit('update-password', { ...passwordForm.value });
    closeAllModals();
}
</script>

<style>
/* Profile-specific styles — prefixed to avoid conflicts */
.edit-btn {
    background: none;
    border: none;
    color: #2d6a4f;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.8px;
    cursor: pointer;
    padding: 0;
    flex-shrink: 0;
    font-family: 'Public Sans', sans-serif;
}
.edit-btn:hover {
    text-decoration: underline;
}
.login-field {
    display: flex;
    align-items: center;
    gap: 16px;
}
.login-field-inner {
    flex: 1;
}
.login-field-inner label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.8px;
    color: #aaa;
    margin-bottom: 6px;
}

/* Modal */
.profile-modal-overlay {
    position: fixed !important;
    inset: 0 !important;
    background: rgba(0, 0, 0, 0.45) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    z-index: 99999 !important;
}
.profile-modal {
    background: #fff;
    border-radius: 12px;
    padding: 36px 32px 28px;
    width: 100%;
    max-width: 560px;
    position: relative;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.18);
}
.profile-modal-close {
    position: absolute;
    top: -16px;
    right: -16px;
    background: #1f4e30;
    border: none;
    border-radius: 6px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
.profile-modal-title {
    font-size: 22px;
    font-weight: 700;
    color: #1b1b3e;
    margin: 0 0 24px;
}
.profile-modal-field {
    margin-bottom: 16px;
}
.profile-modal-field > label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.8px;
    color: #aaa;
    margin-bottom: 6px;
}
.profile-modal-input {
    width: 100%;
    border: 1.5px solid #ddd;
    border-radius: 8px;
    padding: 12px 14px;
    font-size: 14px;
    color: #333;
    outline: none;
    box-sizing: border-box;
    transition: border-color 0.2s;
    font-family: 'Public Sans', sans-serif;
}
.profile-modal-input:focus {
    border-color: #2d6a4f;
}
.profile-dob-row {
    display: flex;
    gap: 12px;
}
.profile-dob-group {
    flex: 1;
}
.profile-dob-label {
    display: block;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.7px;
    color: #aaa;
    margin-bottom: 4px;
}
.profile-dob-input {
    text-align: center;
}
.profile-gender-row {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.profile-gender-option {
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1.5px solid #ddd;
    border-radius: 50px;
    padding: 10px 20px;
    cursor: pointer;
    transition: border-color 0.2s;
}
.profile-gender-option:has(.profile-gender-radio:checked) {
    border-color: #2d6a4f;
}
.profile-gender-radio {
    accent-color: #2d6a4f;
    width: 16px;
    height: 16px;
}
.profile-gender-label {
    font-size: 13px;
    font-weight: 500;
    color: #333;
}
.profile-btn-primary {
    width: 100%;
    background: #1f4e30;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 16px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 1px;
    cursor: pointer;
    margin-top: 8px;
    transition: background 0.2s;
    font-family: 'Public Sans', sans-serif;
}
.profile-btn-primary:hover {
    background: #174023;
}
.profile-btn-secondary {
    width: 100%;
    background: #fff;
    color: #1f4e30;
    border: 1.5px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 1px;
    cursor: pointer;
    margin-top: 10px;
    transition: border-color 0.2s;
    font-family: 'Public Sans', sans-serif;
}
.profile-btn-secondary:hover {
    border-color: #1f4e30;
}
</style>