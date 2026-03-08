/**
 * Converts a string into a URL-friendly slug.
 * @param {string} str
 * @returns {string}
 */
function slugify(str) {
    return str
        .toLowerCase()
        .trim()
        .replace(/\s+/g, '-') // spaces to hyphens
        .replace(/[^a-z0-9-]/g, ''); // remove non-url chars (optional)
}

/**
 * Capitalizes the first letter of each word in a string.
 * @param {string} str
 * @returns {string}
 */
function capitalizeWords(str) {
    return str
        .toLowerCase()
        .split(' ')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

/**
 * Pluralizes a string based on count.
 * @param {string} str - The string to pluralize
 * @param {number|null} count - Optional count to determine if plural is needed
 * @returns {string}
 */
function pluralize(str, count = null) {
    // If no count provided, always pluralize
    if (count === null || count !== 1) {
        // Handle common irregular plurals
        const irregulars = {
            person: 'people',
            man: 'men',
            woman: 'women',
            child: 'children',
            tooth: 'teeth',
            foot: 'feet',
            mouse: 'mice',
            goose: 'geese',
        };

        const lower = str.toLowerCase();
        if (irregulars[lower]) {
            return str.charAt(0) === str.charAt(0).toUpperCase()
                ? irregulars[lower].charAt(0).toUpperCase() +
                      irregulars[lower].slice(1)
                : irregulars[lower];
        }

        // Words ending in 'y' preceded by a consonant
        if (/[^aeiou]y$/i.test(str)) {
            return str.slice(0, -1) + 'ies';
        }

        // Words ending in 's', 'ss', 'sh', 'ch', 'x', 'z'
        if (/(s|ss|sh|ch|x|z)$/i.test(str)) {
            return str + 'es';
        }

        // Words ending in 'f' or 'fe'
        if (/f$/i.test(str)) {
            return str.slice(0, -1) + 'ves';
        }
        if (/fe$/i.test(str)) {
            return str.slice(0, -2) + 'ves';
        }

        // Default: just add 's'
        return str + 's';
    }

    return str;
}

function getModelName(classPath) {
    if (!classPath) return '';
    const parts = classPath.split('\\');
    const raw = parts[parts.length - 1] || '';
    // Insert space before capital letters (except first), also handle camelCase
    return raw
        .replace(/_/g, ' ')
        .replace(/([a-z])([A-Z])/g, '$1 $2')
        .replace(/([A-Z])([A-Z][a-z])/g, '$1 $2')
        .toLowerCase();
}

export { capitalizeWords, getModelName, pluralize, slugify };
