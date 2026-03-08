const fallbackImage = '/img/brand/brand-icon-01.png';
const fallbackUserImage = '/img/default.png';

function getImageUrl(media) {
    if (!media || !media.original_url) {
        return fallbackImage;
    }
    return media.original_url;
}

function getImagePreview(media) {
    if (!media || !media.preview_url) {
        return fallbackImage;
    }
    return media.preview_url;
}

export { fallbackImage, fallbackUserImage, getImagePreview, getImageUrl };
