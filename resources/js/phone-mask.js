/**
 * Auto-formatting input mask for Indonesian phone numbers:
 * Formats: 08xx-xxxx-xxxx / 08xx-xxxx-xxxxx (e.g. 0823-2323-2323)
 */

export function formatIndoPhoneNumber(value) {
    if (!value) return '';
    let digits = String(value).replace(/\D/g, '');
    
    // Normalize +62 or 62 to 0
    if (digits.startsWith('62')) {
        digits = '0' + digits.slice(2);
    }

    // Limit to max 13 digits (standard Indonesian mobile length: 10 to 13 digits)
    digits = digits.slice(0, 13);

    const parts = [];
    if (digits.length > 0) {
        parts.push(digits.slice(0, 4));
    }
    if (digits.length > 4) {
        parts.push(digits.slice(4, 8));
    }
    if (digits.length > 8) {
        parts.push(digits.slice(8, 13));
    }

    return parts.join('-');
}

export function initPhoneMask() {
    const inputs = document.querySelectorAll('input[data-phone-mask], input[name="customer_phone"], input[name="phone"]');

    inputs.forEach(input => {
        if (input._phoneMaskInit) return;
        input._phoneMaskInit = true;

        // Initial format on load
        if (input.value) {
            input.value = formatIndoPhoneNumber(input.value);
        }

        // Backspace handling when cursor is immediately after a hyphen '-'
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace') {
                const cursor = input.selectionStart;
                if (cursor > 1 && input.value[cursor - 1] === '-') {
                    e.preventDefault();
                    const val = input.value;
                    const newVal = val.slice(0, cursor - 2) + val.slice(cursor - 1);
                    input.value = formatIndoPhoneNumber(newVal);
                    const newCursor = Math.max(0, cursor - 2);
                    input.setSelectionRange(newCursor, newCursor);
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
        });

        // Live input masking
        input.addEventListener('input', () => {
            const oldVal = input.value;
            const oldCursor = input.selectionStart;
            const formatted = formatIndoPhoneNumber(oldVal);

            input.value = formatted;

            // Smart cursor adjustment
            const diff = formatted.length - oldVal.length;
            let newCursor = oldCursor + diff;
            if (newCursor < 0) newCursor = 0;
            if (newCursor > formatted.length) newCursor = formatted.length;
            input.setSelectionRange(newCursor, newCursor);
        });

        // Paste event handling
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const formatted = formatIndoPhoneNumber(pastedText);
            input.value = formatted;
            input.setSelectionRange(formatted.length, formatted.length);
            input.dispatchEvent(new Event('input', { bubbles: true }));
        });
    });
}

