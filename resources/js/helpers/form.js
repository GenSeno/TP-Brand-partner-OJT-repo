/**
 * Remove empty or null values from an object
 * @param {Object} obj - The object to transform
 * @returns {Object} - New object with empty/null values removed
 */
export function removeEmptyValues(obj) {
    return Object.keys(obj).reduce((acc, key) => {
        const value = obj[key];

        if (value !== null && value !== undefined && value !== '') {
            // Handle arrays
            if (Array.isArray(value)) {
                const cleanedArray = value
                    .map((item) =>
                        typeof item === 'object' && item !== null
                            ? removeEmptyValues(item)
                            : item,
                    )
                    .filter(
                        (item) =>
                            item !== null && item !== undefined && item !== '',
                    );
                if (cleanedArray.length > 0) {
                    acc[key] = cleanedArray;
                }
            }
            // Handle objects
            else if (typeof value === 'object') {
                const cleanedObj = removeEmptyValues(value);
                if (Object.keys(cleanedObj).length > 0) {
                    acc[key] = cleanedObj;
                }
            }
            // Handle primitives
            else {
                acc[key] = value;
            }
        }

        return acc;
    }, {});
}
