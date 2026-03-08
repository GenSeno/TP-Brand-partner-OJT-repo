import axios from 'axios';
import { reactive } from 'vue';

/**
 * Creates a form handler with built-in state management, validation, and HTTP request capabilities.
 *
 * @template T - Type of the form data
 * @param {T} [initialData={}] - Initial form data values
 * @returns {{
 *   data: T,
 *   errors: Record<keyof T, string>,
 *   processing: boolean,
 *   recentlySuccessful: boolean,
 *   isDirty: boolean,
 *   reset: (...fields: (keyof T)[]) => typeof formMethods,
 *   clearErrors: (...fields: string[]) => typeof formMethods,
 *   setError: (field: string, message: string) => typeof formMethods,
 *   transform: (callback: (data: T) => T) => typeof formMethods,
 *   submit: (method: string, url: string, options?: object) => Promise<any>,
 *   get: (url: string, options?: object) => Promise<any>,
 *   post: (url: string, options?: object) => Promise<any>,
 *   put: (url: string, options?: object) => Promise<any>,
 *   patch: (url: string, options?: object) => Promise<any>,
 *   delete: (url: string, options?: object) => Promise<any>,
 *   on: (event: 'start' | 'success' | 'error' | 'finish', callback: Function) => typeof formMethods
 * }} Form methods and reactive state
 */
export function useAxiosForm(initialData = {}) {
    /** @type {T} Reactive form data object containing all form fields */
    const data = reactive({ ...initialData });

    /** @type {T} Original data snapshot for dirty checking */
    const originalData = JSON.parse(JSON.stringify(initialData));

    /** @type {{errors: Record<keyof T, string>, processing: boolean, recentlySuccessful: boolean, events: Record<string, Function[]>, isDirty: boolean}} Internal state management */
    const state = reactive({
        /** @type {Record<keyof T, string>} Validation errors (field name as key, error message as value) */
        errors: {},
        /** @type {boolean} Indicates if a request is in progress */
        processing: false,
        /** @type {boolean} Indicates if the last request was successful (auto-resets after 2s) */
        recentlySuccessful: false,
        /** @type {Record<string, Function[]>} Internal event listeners registry */
        events: {},
        /** @type {boolean} Indicates if form data has been modified */
        isDirty: false,
    });

    /**
     * Registers an event listener
     * @param {'start' | 'success' | 'error' | 'finish'} event - Event name
     * @param {Function} callback - Callback function to execute when event is emitted
     * @returns {object} Form methods for chaining
     */
    const on = function (event, callback) {
        if (!state.events[event]) {
            state.events[event] = [];
        }
        state.events[event].push(callback);
        return form;
    };

    /**
     * Emits an event to all registered listeners
     * @param {string} event - Event name
     * @param {...any} args - Arguments to pass to callbacks
     */
    const emit = (event, ...args) => {
        if (state.events[event]) {
            state.events[event].forEach((callback) => callback(...args));
        }
    };

    /**
     * Normalize values for comparison (convert string numbers to numbers)
     */
    const normalizeValue = (value, seen = new WeakSet()) => {
        if (value === null || value === undefined) return value;

        // Convert numeric strings to numbers
        if (typeof value === 'string' && !isNaN(value) && value.trim() !== '') {
            return Number(value);
        }

        // Recursively normalize arrays
        if (Array.isArray(value)) {
            return value.map((v) => normalizeValue(v, seen));
        }

        // Recursively normalize objects (prevent circular references)
        if (typeof value === 'object' && value !== null) {
            // Check for circular reference
            if (seen.has(value)) {
                return '[Circular]';
            }
            seen.add(value);

            const normalized = {};
            for (const key in value) {
                if (Object.prototype.hasOwnProperty.call(value, key)) {
                    normalized[key] = normalizeValue(value[key], seen);
                }
            }
            return normalized;
        }

        return value;
    };

    /**
     * Checks if form data has been modified from original values
     * @returns {boolean} True if data has changed
     */
    const checkDirty = () => {
        try {
            // Convert reactive objects to plain objects first to avoid recursion
            const plainData = JSON.parse(JSON.stringify(data));
            const plainOriginal = JSON.parse(JSON.stringify(originalData));

            const normalizedData = normalizeValue(plainData);
            const normalizedOriginal = normalizeValue(plainOriginal);

            state.isDirty =
                JSON.stringify(normalizedData) !==
                JSON.stringify(normalizedOriginal);
        } catch (e) {
            // If comparison fails, assume dirty
            state.isDirty = true;
        }
        return state.isDirty;
    };

    /**
     * Resets form data to initial values
     * @param {...(keyof T)} [fields] - Optional field names to reset. If empty, resets all fields
     * @returns {object} Form methods for chaining
     */
    const reset = function (...fields) {
        if (fields.length === 0) {
            Object.assign(data, initialData);
            Object.assign(
                originalData,
                JSON.parse(JSON.stringify(initialData)),
            );
        } else {
            fields.forEach((field) => {
                data[field] = initialData[field];
            });
        }
        state.errors = {};
        checkDirty();
        return form;
    };

    /**
     * Clears validation errors
     * @param {...string} [fields] - Optional field names to clear errors for. If empty, clears all errors
     * @returns {object} Form methods for chaining
     */
    const clearErrors = function (...fields) {
        if (fields.length === 0) {
            state.errors = {};
        } else {
            fields.forEach((field) => {
                delete state.errors[field];
            });
        }
        return form;
    };

    /**
     * Sets an error message for a specific field
     * @param {string} field - Field name to set error for
     * @param {string} message - Error message
     * @returns {object} Form methods for chaining
     */
    const setError = function (field, message) {
        state.errors[field] = message;
        return form;
    };

    /**
     * Transforms form data using a callback function
     * @param {(data: T) => T} callback - Function that receives current data and returns transformed data
     * @returns {object} Form methods for chaining
     */
    const transform = function (callback) {
        const transformed = callback(data);
        Object.assign(data, transformed);
        return form;
    };

    // --- File upload helpers -------------------------------------------------
    function isFileValue(val) {
        if (typeof File !== 'undefined' && val instanceof File) return true;
        if (typeof Blob !== 'undefined' && val instanceof Blob) return true;
        if (typeof FileList !== 'undefined' && val instanceof FileList)
            return true;
        return false;
    }

    function hasFile(obj) {
        if (!obj || typeof obj !== 'object') return false;
        if (isFileValue(obj)) return true;

        for (const key in obj) {
            if (!Object.prototype.hasOwnProperty.call(obj, key)) continue;
            const val = obj[key];
            if (isFileValue(val)) return true;
            if (Array.isArray(val)) {
                for (const el of val) {
                    if (hasFile(el)) return true;
                }
            } else if (val && typeof val === 'object') {
                if (hasFile(val)) return true;
            }
        }
        return false;
    }

    function appendFormData(formData, value, parentKey = '') {
        if (isFileValue(value)) {
            // FileList -> append every file
            if (typeof FileList !== 'undefined' && value instanceof FileList) {
                for (let i = 0; i < value.length; i++) {
                    formData.append(parentKey, value[i]);
                }
            } else {
                formData.append(parentKey, value);
            }
            return;
        }

        if (value === null || value === undefined) {
            formData.append(parentKey, '');
            return;
        }

        if (typeof value === 'boolean') {
            formData.append(parentKey, value ? '1' : '0');
            return;
        }

        if (Array.isArray(value)) {
            value.forEach((v) => {
                appendFormData(formData, v, parentKey + '[]');
            });
            return;
        }

        if (typeof value === 'object') {
            Object.keys(value).forEach((key) => {
                const newKey = parentKey ? `${parentKey}[${key}]` : key;
                appendFormData(formData, value[key], newKey);
            });
            return;
        }

        formData.append(parentKey, value);
    }
    // -------------------------------------------------------------------------

    /**
     * Submits form data using specified HTTP method
     * @param {string} method - HTTP method (get, post, put, patch, delete)
     * @param {string} url - Request URL
     * @param {object} [options={}] - Additional options
     * @param {Function} [options.onSuccess] - Callback executed on successful request
     * @param {Function} [options.onError] - Callback executed on request error
     * @param {Function} [options.onFinish] - Callback executed after request completes
     * @returns {Promise<any>} Axios response object
     * @throws {Error} Throws error if request fails
     */
    const submit = async (method, url, options = {}) => {
        const { onStart, onSuccess, onError, onFinish, ...axiosOptions } =
            options;

        state.processing = true;
        state.errors = {};
        state.recentlySuccessful = false;

        emit('start');
        onStart?.();

        try {
            const isGet = method.toLowerCase() === 'get';
            const { headers: optHeaders, ...restAxiosOptions } =
                axiosOptions || {};
            const headers = optHeaders ? { ...optHeaders } : {};

            let payload = undefined;
            if (!isGet) {
                if (hasFile(data)) {
                    const fd = new FormData();
                    appendFormData(fd, data);
                    payload = fd;
                    // let browser/axios set Content-Type with boundary
                    if (headers['Content-Type']) delete headers['Content-Type'];
                    if (headers['content-type']) delete headers['content-type'];
                } else {
                    payload = data;
                }
            }

            const response = await axios({
                method,
                url,
                data: payload,
                params: isGet ? data : undefined,
                headers,
                ...restAxiosOptions,
            });

            state.processing = false;
            state.recentlySuccessful = true;

            emit('success', response);
            onSuccess?.(response);

            setTimeout(() => {
                state.recentlySuccessful = false;
            }, 2000);

            return response;
        } catch (error) {
            state.processing = false;

            if (error.response?.data?.errors) {
                Object.entries(error.response.data.errors).forEach(([k, v]) => {
                    state.errors[k] = v[0];
                });
            } else if (error.response?.data?.message) {
                state.errors.general = error.response.data.message;
            }

            emit('error', error);
            onError?.(error);

            // throw error;
        } finally {
            emit('finish');
            onFinish?.();
        }
    };

    /**
     * Sends a GET request
     * @param {string} url - Request URL
     * @param {object} [options] - Additional axios options
     * @returns {Promise<any>} Axios response object
     */
    const get = (url, options) => submit('get', url, options);

    /**
     * Sends a POST request
     * @param {string} url - Request URL
     * @param {object} [options] - Additional axios options
     * @returns {Promise<any>} Axios response object
     */
    const post = (url, options) => submit('post', url, options);

    /**
     * Sends a PUT request
     * @param {string} url - Request URL
     * @param {object} [options] - Additional axios options
     * @returns {Promise<any>} Axios response object
     */
    const put = (url, options) => submit('put', url, options);

    /**
     * Sends a PATCH request
     * @param {string} url - Request URL
     * @param {object} [options] - Additional axios options
     * @returns {Promise<any>} Axios response object
     */
    const patch = (url, options) => submit('patch', url, options);

    /**
     * Sends a DELETE request
     * @param {string} url - Request URL
     * @param {object} [options] - Additional axios options
     * @returns {Promise<any>} Axios response object
     */
    const del = (url, options) => submit('delete', url, options);

    const form = {
        data,
        get errors() {
            return state.errors;
        },
        get processing() {
            return state.processing;
        },
        get recentlySuccessful() {
            return state.recentlySuccessful;
        },
        get isDirty() {
            checkDirty();
            return state.isDirty;
        },

        reset,
        clearErrors,
        setError,
        transform,
        submit,
        get,
        post,
        put,
        patch,
        delete: del,
        on,
    };

    return form;
}
