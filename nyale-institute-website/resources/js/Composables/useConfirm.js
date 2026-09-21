import { reactive } from 'vue';

export const confirmState = reactive({
    open: false,
    title: '',
    message: '',
    confirmText: 'Confirm',
    cancelText: 'Cancel',
    tone: 'danger',
    resolve: null,
});

// Opens the confirmation pop-up and resolves to true (confirmed) or false (cancelled).
export function confirmAction({
    title = 'Are you sure?',
    message = '',
    confirmText = 'Confirm',
    cancelText = 'Cancel',
    tone = 'danger',
} = {}) {
    return new Promise((resolve) => {
        // A new request replaces any dialog that is still open.
        confirmState.resolve?.(false);
        Object.assign(confirmState, { open: true, title, message, confirmText, cancelText, tone, resolve });
    });
}

export function settleConfirm(result) {
    const { resolve } = confirmState;
    confirmState.open = false;
    confirmState.resolve = null;
    resolve?.(result);
}

// Shortcut for the common "delete this record" prompt.
export const confirmDelete = (title, message = 'This action cannot be undone.') =>
    confirmAction({ title, message, confirmText: 'Delete', tone: 'danger' });
