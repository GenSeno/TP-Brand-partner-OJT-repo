import { emitter } from '@/composables/eventBus';

/**
 * Display success alert
 * @param {string} message - Success message to display
 */
export function showSuccess(message) {
    emitter.emit('toast:show', { type: 'success', message });
}

/**
 * Display error alert
 * @param {string} message - Error message to display
 */
export function showError(message) {
    emitter.emit('toast:show', { type: 'error', message });
}

/**
 * Display info alert
 * @param {string} message - Info message to display
 */
export function showInfo(message) {
    emitter.emit('toast:show', { type: 'info', message });
}

/**
 * Display warning alert
 * @param {string} message - Warning message to display
 */
export function showWarning(message) {
    emitter.emit('toast:show', { type: 'warning', message });
}
