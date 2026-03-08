<style scoped>
.profile-pic-upload .profile-pic.brand-pic img {
    max-width: 110px;
    max-height: 105px;
    object-fit: cover;
    border-radius: 10px;
}
</style>

<template>
    <div class="profile-pic-upload">
        <div class="profile-pic brand-pic">
            <template v-if="preview">
                <span><img :src="preview" alt="" class="img-fluid" /></span>
                <a
                    href="javascript:void(0);"
                    class="remove-photo"
                    @click="removeImage"
                >
                    <vue-feather type="x" class="x-square-add"></vue-feather>
                </a>
            </template>
            <template v-else>
                <span
                    ><vue-feather
                        type="plus-circle"
                        class="plus-down-add"
                    ></vue-feather>
                    Add Image</span
                >
            </template>
        </div>
        <div>
            <div class="image-upload mb-0">
                <input
                    @change="onFileChange"
                    type="file"
                    :accept="acceptType"
                />
                <div class="image-uploads">
                    <h4>{{ preview ? 'Change Image' : 'Add Image' }}</h4>
                </div>
            </div>
            <p class="mt-2 mb-0">{{ acceptLabels }} up to {{ megabytes }} MB</p>
            <input-error :message="customErrorMessage" />
        </div>
    </div>
</template>

<script setup>
import { computed, watch } from 'vue';
import { onMounted, ref } from 'vue';

const kilobytes = 1024;
const avatar = defineModel('avatar', { required: true });
const logoRemoved = defineModel('logoRemoved');
const preview = ref(null);
const emit = defineEmits(['update:avatar', 'update:logoRemoved']);

const props = defineProps({
    acceptType: {
        type: String,
        default: 'image/*',
    },
    errorMessage: {
        type: String,
        default: '',
    },
    shouldPreview: {
        type: Boolean,
        default: true,
    },
    defaultImage: {
        type: String,
        default: null,
    },
    maxFileSizeKB: {
        type: Number,
        default: 2048,
    },
});

const megabytes = computed(() => props.maxFileSizeKB / kilobytes);

const customErrorMessage = ref('');

const acceptLabels = computed(() => {
    const types = props.acceptType.split(',');
    return types
        .map((type) => {
            if (type === 'image/*') return 'Images';
            if (type === 'application/pdf') return 'PDFs';
            if (
                type === 'application/msword' ||
                type ===
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            )
                return 'Word Documents';
            return type.toUpperCase();
        })
        .join(', ');
});

function onFileChange(e) {
    const file = e.target.files[0];

    customErrorMessage.value = '';

    if (!file) {
        return;
    }

    if (file.size > props.maxFileSizeKB * kilobytes) {
        customErrorMessage.value = `Size limit exceeded (${megabytes.value} MB)`;
        return;
    }

    const acceptedTypes = props.acceptType.split(',').map((t) => t.trim());
    const isValidType = acceptedTypes.some((type) => {
        if (type === 'image/*') return file.type.startsWith('image/');
        if (type === 'application/*')
            return file.type.startsWith('application/');
        if (type.endsWith('/*'))
            return file.type.startsWith(type.replace('/*', '/'));
        return file.type === type;
    });

    if (!isValidType) {
        customErrorMessage.value = 'File type is not accepted';
        return;
    }

    avatar.value = file;
    emit('update:avatar', file);

    const reader = new FileReader();
    reader.onload = () => {
        preview.value = reader.result;
    };
    reader.readAsDataURL(file);

    if (logoRemoved.value) {
        logoRemoved.value = false;
    }
}

function removeImage() {
    preview.value = null;
    customErrorMessage.value = '';

    if (avatar.value) {
        avatar.value = '';
    }

    if (!logoRemoved.value) {
        logoRemoved.value = true;
    }

    emit('update:logoRemoved', true);
}

onMounted(() => {
    if (props.shouldPreview && props.defaultImage) {
        preview.value = props.defaultImage;
    }
});

watch(
    () => props.errorMessage,
    (newVal) => {
        customErrorMessage.value = newVal;
    },
);
</script>
