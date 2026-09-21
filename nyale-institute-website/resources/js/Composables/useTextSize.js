import { ref } from 'vue';

const STORAGE_KEY = 'nyale-dashboard-text-step';

// Step 0 is normal; each step above it enlarges the dashboard by another 25%.
export const TEXT_STEPS = [
    { step: 0, label: 'Normal text', short: 'Normal', scale: '' },
    { step: 1, label: 'Larger text (+25%)', short: '+25%', scale: '125%' },
    { step: 2, label: 'Largest text (+50%)', short: '+50%', scale: '150%' },
];

const read = () => {
    try {
        const saved = Number(localStorage.getItem(STORAGE_KEY));
        return TEXT_STEPS.some((s) => s.step === saved) ? saved : 0;
    } catch (e) {
        return 0;
    }
};

// Shared across dashboard pages so the choice survives navigation.
const step = ref(read());
let resetTimer = null;

const apply = () => {
    document.documentElement.style.fontSize = TEXT_STEPS[step.value].scale;
};

// All spacing and text in the dashboard is rem-based, so scaling the root size enlarges everything together.
export function useTextSize() {
    const setStep = (value) => {
        step.value = value;
        try {
            localStorage.setItem(STORAGE_KEY, String(value));
        } catch (e) { /* preference simply won't persist */ }
        apply();
    };

    // Called when a dashboard page mounts. Cancels a pending reset so moving between dashboard pages doesn't flicker.
    const activate = () => {
        clearTimeout(resetTimer);
        apply();
    };

    // Called when a dashboard page unmounts. The public site keeps its normal size unless another dashboard page mounts right away.
    const release = () => {
        clearTimeout(resetTimer);
        resetTimer = setTimeout(() => { document.documentElement.style.fontSize = ''; }, 0);
    };

    return { step, setStep, activate, release };
}
