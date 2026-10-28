import { createIcons, icons } from 'lucide';
import { initNeoSelect } from './neo-select';
import { initNeoDatePicker } from './neo-datepicker';
import { initPhoneMask } from './phone-mask';

// Initialize Lucide icons, Neo-Select, Neo-DatePicker, and PhoneMask components on page load
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
    initNeoSelect();
    initNeoDatePicker();
    initPhoneMask();
});

// Expose a helper to re-trigger icon rendering when dynamic elements are added
window.refreshIcons = () => {
    createIcons({ icons });
};

window.refreshNeoSelect = () => {
    initNeoSelect();
};

window.refreshNeoDatePicker = () => {
    initNeoDatePicker();
};

window.refreshPhoneMask = () => {
    initPhoneMask();
};


