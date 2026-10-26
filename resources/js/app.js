import { createIcons, icons } from 'lucide';

// Initialize Lucide icons on page load
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});

// Expose a helper to re-trigger icon rendering when dynamic elements are added
window.refreshIcons = () => {
    createIcons({ icons });
};
