// Cache formatters for better performance
const numberFormatters = new Map();
const currencyFormatters = new Map();
const percentageFormatters = new Map();

/**
 * Format a number with thousand separators
 * @param {number} value - The number to format
 * @param {number} decimals - Number of decimal places (default: 0)
 * @returns {string} Formatted number
 */
export function formatNumber(value, decimals = 0) {
    if (isNaN(value)) return '0';

    const key = decimals;
    if (!numberFormatters.has(key)) {
        numberFormatters.set(
            key,
            new Intl.NumberFormat('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
            }),
        );
    }

    return numberFormatters.get(key).format(value);
}

/**
 * Format a BigInt as money with thousand separators
 * @param {BigInt} value - The BigInt to format as money
 * @param {number} decimals - Number of decimal places (default: 2)
 * @returns {string} Formatted money string
 */
export function formatMoney(value, decimals = 2) {
    if (typeof value !== 'bigint') return '0.00';

    const divisor = BigInt(Math.pow(10, decimals));
    const integerPart = value / divisor;
    const fractionalPart = value % divisor;

    const formattedInteger = formatNumber(Number(integerPart), 0);
    const formattedFractional = fractionalPart
        .toString()
        .padStart(decimals, '0');

    return `${formattedInteger}.${formattedFractional}`;
}

/**
 * Format a number as currency
 * @param {number|object} value - The amount to format (can be a Price object with .value or .formatted, or a number)
 * @param {string} currency - Currency code (default: 'PHP')
 * @param {string} locale - Locale for formatting (default: 'en-PH')
 * @returns {string} Formatted currency
 */
export function formatCurrency(value, currency = 'PHP', locale = 'en-PH') {
    // If value is a Price object with formatted property, use it directly
    if (value && typeof value === 'object' && 'formatted' in value) {
        return value.formatted;
    }

    // If value is a Price object with value property, extract it
    const numericValue =
        value && typeof value === 'object' && 'value' in value
            ? value.value
            : value;

    const key = `${locale}-${currency}`;
    if (!currencyFormatters.has(key)) {
        currencyFormatters.set(
            key,
            new Intl.NumberFormat(locale, {
                style: 'currency',
                currency: currency,
            }),
        );
    }

    // Convert from integer cents to decimal
    const decimalValue = Number.parseFloat(numericValue) / 100;
    return currencyFormatters.get(key).format(decimalValue);
}

/**
 * Format a number as percentage
 * @param {number} value - The number to format (e.g., 0.25 for 25%)
 * @param {number} decimals - Number of decimal places (default: 2)
 * @returns {string} Formatted percentage
 */
export function formatPercentage(value, decimals = 2) {
    if (isNaN(value)) return '0%';

    const key = decimals;
    if (!percentageFormatters.has(key)) {
        percentageFormatters.set(
            key,
            new Intl.NumberFormat('en-US', {
                style: 'percent',
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
            }),
        );
    }

    return percentageFormatters.get(key).format(value);
}

/**
 * Format file size in bytes to human readable format
 * @param {number} bytes - Size in bytes
 * @param {number} decimals - Number of decimal places (default: 2)
 * @returns {string} Formatted size
 */
export function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return (
        parseFloat((bytes / Math.pow(k, i)).toFixed(decimals)) + ' ' + sizes[i]
    );
}

/**
 * Abbreviate large numbers (e.g., 1000 -> 1K, 1000000 -> 1M)
 * @param {number} value - The number to abbreviate
 * @param {number} decimals - Number of decimal places (default: 1)
 * @returns {string} Abbreviated number
 */
export function abbreviateNumber(value, decimals = 1) {
    if (isNaN(value)) return '0';
    if (value < 1000) return value.toString();

    const suffixes = ['', 'K', 'M', 'B', 'T'];
    const tier = Math.floor(Math.log10(Math.abs(value)) / 3);
    const suffix = suffixes[tier];
    const scale = Math.pow(10, tier * 3);
    const scaled = value / scale;

    return scaled.toFixed(decimals) + suffix;
}

/**
 * Parse formatted number string to number
 * @param {string} value - Formatted number string
 * @returns {number} Parsed number
 */
export function parseFormattedNumber(value) {
    if (typeof value === 'number') return value;
    return parseFloat(value.replace(/[^0-9.-]+/g, '')) || 0;
}

/**
 * Simplify floating numbers by removing trailing zeros after decimal point
 * @param {number} value - The number to simplify
 * @param {number} maxDecimals - Maximum decimal places to keep (default: 10)
 * @returns {number} Simplified number
 */
export function simplifyFloat(value, maxDecimals = 10) {
    value = Number(value);
    if (isNaN(value)) return 0;
    // Convert to number, then back to remove trailing zeros
    return parseFloat(value.toFixed(maxDecimals));
}

/**
 * Format file size in bytes to human readable format
 * @param {number} bytes - Size in bytes
 * @returns {string} Formatted size
 */
export function formatSize(bytes) {
    if (!bytes) return '';
    const sizes = ['B', 'KB', 'MB', 'GB'];
    let i = 0;
    let size = bytes;
    while (size >= 1024 && i < sizes.length - 1) {
        size /= 1024;
        i++;
    }
    return size.toFixed(2) + ' ' + sizes[i];
}
