import { createIcons, icons } from 'lucide';
import { initNeoSelect } from './neo-select';

// Initialize Lucide icons and Neo-Select components on page load
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
    initNeoSelect();
});

// Expose a helper to re-trigger icon rendering when dynamic elements are added
window.refreshIcons = () => {
    createIcons({ icons });
};

window.refreshNeoSelect = () => {
    initNeoSelect();
};

