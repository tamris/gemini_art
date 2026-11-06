/**
 * Soft Neo-Brutalism Custom Select & Seamless Live Filter Engine
 * 
 * Features:
 * 1. Global coverage: Automatically transforms all selects into Soft Neo-Brutalism dropdowns.
 * 2. Butter-smooth cubic-bezier transitions for opening & closing.
 * 3. Persistent option DOM nodes: zero innerHTML destruction during closing animations.
 * 4. Prevents viewport jumps: internal menu scrolling without window scrollIntoView snapping.
 * 5. Native form validation support: displays subtle rose border on required inputs.
 * 6. Silky live AJAX filtering for filter forms with debounced loading states (zero flicker).
 * 7. Automatic MutationObserver support for dialogs and dynamic elements.
 */

let filterDebounceTimer = null;
let currentAbortController = null;

export function handleLiveFilter(form, options = {}) {
    const resultsContainer = document.getElementById('filter-results-container');
    if (!resultsContainer) {
        form.submit();
        return;
    }

    // Cancel any previous pending live request to prevent race conditions
    if (currentAbortController) {
        currentAbortController.abort();
    }
    currentAbortController = new AbortController();

    const formData = new FormData(form);
    const params = new URLSearchParams();
    for (const [key, value] of formData.entries()) {
        if (value !== '') {
            params.append(key, value);
        }
    }
    const queryString = params.toString();
    const url = form.action + (queryString ? '?' + queryString : '');

    // Debounced subtle loading indicator:
    // If the response is fast (<100ms), opacity does NOT drop at all (zero flicker / blink).
    // If it takes longer, it gently dims to 0.7 without harsh blackouts.
    clearTimeout(filterDebounceTimer);
    filterDebounceTimer = setTimeout(() => {
        resultsContainer.style.transition = 'opacity 180ms ease';
        resultsContainer.style.opacity = '0.7';
    }, 100);

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        signal: currentAbortController.signal
    })
    .then(res => res.text())
    .then(html => {
        clearTimeout(filterDebounceTimer);
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newResults = doc.getElementById('filter-results-container');

        if (newResults) {
            resultsContainer.innerHTML = newResults.innerHTML;
            if (options && options.replaceState) {
                window.history.replaceState(null, '', url);
            } else {
                window.history.pushState(null, '', url);
            }

            // Re-render Lucide icons inside newly swapped HTML
            if (window.refreshIcons) {
                window.refreshIcons();
            }

            // Sync Reset Filter button state smoothly
            const actionPath = form.getAttribute('action') || '';
            const newReset = doc.querySelector(`a[href*="${actionPath}"][title*="Reset"], a[href*="${actionPath}"].neo-btn`);
            const curReset = form.querySelector(`a[href*="${actionPath}"]`);
            if (newReset && curReset) {
                curReset.className = newReset.className;
            }

            // Smooth fade-in
            resultsContainer.style.transition = 'opacity 180ms ease';
            resultsContainer.style.opacity = '1';
        } else {
            // Fallback to normal navigation if target container not found
            window.location.href = url;
        }
    })
    .catch(err => {
        if (err.name === 'AbortError') return;
        form.submit();
    })
    .finally(() => {
        clearTimeout(filterDebounceTimer);
        resultsContainer.style.opacity = '1';
        resultsContainer.style.pointerEvents = 'auto';
    });
}

export function initNeoSelect() {
    // 1. Initialize Server-Rendered Neo-Selects (Frame 0: Zero DOM Shift, Zero Layout Shift, Zero Flicker)
    const preRenderedWrappers = document.querySelectorAll('.neo-select-wrapper[data-neo-select-wrapper]');
    preRenderedWrappers.forEach(wrapper => {
        if (wrapper.dataset.neoSelectInitialized === 'true') {
            return;
        }
        wrapper.dataset.neoSelectInitialized = 'true';

        const select = wrapper.querySelector('select');
        const trigger = wrapper.querySelector('.neo-select-trigger');
        const labelSpan = wrapper.querySelector('.neo-select-label');
        const chevron = wrapper.querySelector('.neo-select-chevron');
        const menu = wrapper.querySelector('.neo-select-menu');
        if (!select || !trigger || !menu) return;

        const optElements = Array.from(menu.querySelectorAll('[role="option"]'));

        function updateStates(activeVal) {
            trigger.classList.remove('border-rose-500', 'ring-2', 'ring-rose-400');
            optElements.forEach(el => {
                const isSelected = String(el.dataset.value) === String(activeVal);
                el.setAttribute('aria-selected', isSelected ? 'true' : 'false');
                const check = el.querySelector('.neo-select-check');
                if (isSelected) {
                    el.className = 'neo-select-option px-3.5 py-2.5 text-xs font-black cursor-pointer flex items-center justify-between gap-2.5 border-l-4 border-amber-500 bg-amber-100/90 dark:bg-amber-950/40 text-slate-950 dark:text-amber-300';
                    if (check) check.style.display = 'inline-flex';
                    const textNode = el.querySelector('.truncate');
                    if (labelSpan && textNode) {
                        labelSpan.textContent = textNode.textContent.trim();
                    }
                } else {
                    el.className = 'neo-select-option px-3.5 py-2.5 text-xs font-bold cursor-pointer flex items-center justify-between gap-2.5 border-l-4 border-transparent text-slate-700 dark:text-slate-300 hover:bg-amber-50 dark:hover:bg-slate-800/80 hover:text-slate-950 dark:hover:text-white';
                    if (check) check.style.display = 'none';
                }
            });
        }

        let closeTimer = null;

        function openMenu() {
            clearTimeout(closeTimer);
            document.querySelectorAll('.neo-select-menu.is-open').forEach(m => {
                if (m !== menu) {
                    m.classList.remove('is-open');
                    const otherWrapper = m.closest('.neo-select-wrapper');
                    const otherTrigger = otherWrapper?.querySelector('.neo-select-trigger');
                    const otherChevron = otherWrapper?.querySelector('.neo-select-chevron');
                    if (otherTrigger) {
                        otherTrigger.classList.remove('border-slate-800', 'dark:border-slate-400', 'shadow-[2.5px_2.5px_0px_0px_#0f172a]', 'dark:shadow-[2.5px_2.5px_0px_0px_#334155]', 'bg-slate-50', 'dark:bg-slate-900');
                        otherTrigger.setAttribute('aria-expanded', 'false');
                    }
                    if (otherChevron) {
                        otherChevron.classList.remove('rotate-180', 'text-slate-800');
                    }
                    if (otherWrapper) otherWrapper.style.zIndex = '';
                }
            });

            wrapper.style.zIndex = '40';
            menu.classList.add('is-open');
            trigger.classList.add('border-slate-800', 'dark:border-slate-400', 'shadow-[2.5px_2.5px_0px_0px_#0f172a]', 'dark:shadow-[2.5px_2.5px_0px_0px_#334155]', 'bg-slate-50', 'dark:bg-slate-900');
            trigger.setAttribute('aria-expanded', 'true');
            if (chevron) chevron.classList.add('rotate-180', 'text-slate-800');

            const activeItem = menu.querySelector('[role="option"][aria-selected="true"]');
            if (activeItem) {
                const menuScrollTop = menu.scrollTop;
                const menuHeight = menu.clientHeight;
                const itemTop = activeItem.offsetTop;
                const itemHeight = activeItem.offsetHeight;

                if (itemTop < menuScrollTop) {
                    menu.scrollTop = itemTop;
                } else if (itemTop + itemHeight > menuScrollTop + menuHeight) {
                    menu.scrollTop = itemTop + itemHeight - menuHeight;
                }
            }
        }

        function closeMenu() {
            menu.classList.remove('is-open');
            trigger.classList.remove('border-slate-800', 'dark:border-slate-400', 'shadow-[2.5px_2.5px_0px_0px_#0f172a]', 'dark:shadow-[2.5px_2.5px_0px_0px_#334155]', 'bg-slate-50', 'dark:bg-slate-900');
            trigger.setAttribute('aria-expanded', 'false');
            if (chevron) chevron.classList.remove('rotate-180', 'text-slate-800');

            clearTimeout(closeTimer);
            closeTimer = setTimeout(() => {
                if (!menu.classList.contains('is-open')) {
                    wrapper.style.zIndex = '';
                }
            }, 180);
        }

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            if (menu.classList.contains('is-open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        optElements.forEach(optEl => {
            optEl.addEventListener('click', (e) => {
                e.stopPropagation();
                const prevValue = select.value;
                const newValue = optEl.dataset.value;

                select.value = newValue;
                updateStates(newValue);
                closeMenu();

                select.dispatchEvent(new Event('change', { bubbles: true }));

                if (prevValue !== newValue) {
                    const form = select.closest('form[data-auto-submit]');
                    if (form) {
                        handleLiveFilter(form);
                    }
                }
            });
        });

        select.addEventListener('change', () => {
            updateStates(select.value);
        });

        const form = select.closest('form');
        if (form) {
            form.addEventListener('reset', () => {
                setTimeout(() => updateStates(select.value), 10);
            });
        }
    });

    // 2. Initialize Dynamic Client-Rendered Selects (fallback for plain selects marked with class neo-select)
    const selects = document.querySelectorAll('select.neo-select:not([data-no-neo-select]):not([data-neo-select-initialized="true"])');

    selects.forEach(select => {
        if (select.closest('.neo-select-wrapper') || select.dataset.neoSelectInitialized === 'true') {
            return;
        }
        select.dataset.neoSelectInitialized = 'true';

        // 1. Create container wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'neo-select-wrapper relative w-full select-none';

        // Insert wrapper before select and move select inside
        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(select);

        // Keep native select submittable but hidden from display
        select.classList.add('sr-only');
        select.style.position = 'absolute';
        select.style.opacity = '0';
        select.style.width = '1px';
        select.style.height = '1px';
        select.style.padding = '0';
        select.style.margin = '-1px';
        select.style.overflow = 'hidden';
        select.style.clip = 'rect(0, 0, 0, 0)';
        select.style.border = '0';
        select.style.pointerEvents = 'none';

        // Determine initial selected option
        let selectedOption = select.options[select.selectedIndex] || select.options[0];
        let selectedText = selectedOption ? selectedOption.textContent.trim() : 'Pilih opsi...';

        // 2. Build Trigger Button (with full dark mode support)
        const trigger = document.createElement('button');
        trigger.type = 'button';
        const heightClass = select.className.match(/\bh-\S+/)?.[0] || 'h-10';
        trigger.className = `neo-select-trigger w-full ${heightClass} flex items-center justify-between gap-2 bg-white dark:bg-[#0c1222] border-[1.5px] border-slate-300 dark:border-slate-600 rounded-xl px-3 text-xs font-bold text-slate-800 dark:text-slate-100 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[2px_2px_0px_0px_#334155] hover:border-slate-800 dark:hover:border-slate-400 focus:outline-none cursor-pointer text-left`;
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');

        const labelSpan = document.createElement('span');
        labelSpan.className = 'neo-select-label truncate';
        labelSpan.textContent = selectedText;

        const chevron = document.createElement('span');
        chevron.className = 'neo-select-chevron text-slate-400 dark:text-slate-500 shrink-0';
        chevron.innerHTML = `<svg class="w-3.5 h-3.5 stroke-[2.5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>`;

        trigger.appendChild(labelSpan);
        trigger.appendChild(chevron);
        wrapper.appendChild(trigger);

        // 3. Build Dropdown Menu
        const menu = document.createElement('div');
        menu.className = 'neo-select-menu absolute top-[calc(100%+6px)] left-0 w-full min-w-full max-h-60 overflow-y-auto bg-white dark:bg-[#0c1222] border-[1.5px] border-slate-800 dark:border-slate-600 rounded-xl shadow-[3.5px_3.5px_0px_0px_#0f172a] dark:shadow-[4px_4px_0px_0px_#334155] py-1.5 z-50';
        menu.setAttribute('role', 'listbox');

        // Store pre-built option DOM elements for instant, stable updates
        const optionElements = [];

        Array.from(select.options).forEach((opt, index) => {
            const optEl = document.createElement('div');
            optEl.setAttribute('role', 'option');
            optEl.dataset.value = opt.value;
            optEl.dataset.index = index;

            const textSpan = document.createElement('span');
            textSpan.className = 'truncate';
            textSpan.textContent = opt.textContent.trim();
            optEl.appendChild(textSpan);

            const checkIcon = document.createElement('span');
            checkIcon.className = 'neo-select-check shrink-0 text-amber-900 dark:text-amber-400';
            checkIcon.innerHTML = `<svg class="w-3.5 h-3.5 stroke-[2.5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>`;
            optEl.appendChild(checkIcon);

            // Click on Option
            optEl.addEventListener('click', (e) => {
                e.stopPropagation();
                const prevValue = select.value;
                const newValue = opt.value;

                select.selectedIndex = index;
                select.value = newValue;

                updateOptionStates(index);
                closeMenu();

                // Dispatch native change event
                select.dispatchEvent(new Event('change', { bubbles: true }));

                // Instant live filter if value changed and inside auto-submit form
                if (prevValue !== newValue) {
                    const form = select.closest('form[data-auto-submit]');
                    if (form) {
                        handleLiveFilter(form);
                    }
                }
            });

            optionElements.push({ el: optEl, check: checkIcon, opt: opt });
            menu.appendChild(optEl);
        });

        // Updates active states without rebuilding DOM nodes
        function updateOptionStates(activeIndex) {
            trigger.classList.remove('border-rose-500', 'ring-2', 'ring-rose-400');
            optionElements.forEach(({ el, check, opt }, idx) => {
                const isSelected = idx === activeIndex;
                el.setAttribute('aria-selected', isSelected ? 'true' : 'false');

                if (isSelected) {
                    el.className = 'neo-select-option px-3.5 py-2.5 text-xs font-black cursor-pointer flex items-center justify-between gap-2.5 border-l-4 border-amber-500 bg-amber-100/90 dark:bg-amber-950/40 text-slate-950 dark:text-amber-300';
                    check.style.display = 'inline-flex';
                    labelSpan.textContent = opt.textContent.trim();
                } else {
                    el.className = 'neo-select-option px-3.5 py-2.5 text-xs font-bold cursor-pointer flex items-center justify-between gap-2.5 border-l-4 border-transparent text-slate-700 dark:text-slate-300 hover:bg-amber-50 dark:hover:bg-slate-800/80 hover:text-slate-950 dark:hover:text-white';
                    check.style.display = 'none';
                }
            });
        }

        updateOptionStates(select.selectedIndex);
        wrapper.appendChild(menu);

        let closeZIndexTimer = null;

        function openMenu() {
            clearTimeout(closeZIndexTimer);

            // Close other open menus smoothly
            document.querySelectorAll('.neo-select-menu.is-open').forEach(m => {
                if (m !== menu) {
                    m.classList.remove('is-open');
                    const otherWrapper = m.closest('.neo-select-wrapper');
                    const otherTrigger = otherWrapper?.querySelector('.neo-select-trigger');
                    const otherChevron = otherWrapper?.querySelector('.neo-select-chevron');
                    if (otherTrigger) {
                        otherTrigger.classList.remove('border-slate-800', 'dark:border-slate-400', 'shadow-[2.5px_2.5px_0px_0px_#0f172a]', 'dark:shadow-[2.5px_2.5px_0px_0px_#334155]', 'bg-slate-50', 'dark:bg-slate-900');
                        otherTrigger.setAttribute('aria-expanded', 'false');
                    }
                    if (otherChevron) {
                        otherChevron.classList.remove('rotate-180', 'text-slate-800');
                    }
                    if (otherWrapper) {
                        otherWrapper.style.zIndex = '';
                    }
                }
            });

            wrapper.style.zIndex = '40';
            menu.classList.add('is-open');
            trigger.classList.add('border-slate-800', 'dark:border-slate-400', 'shadow-[2.5px_2.5px_0px_0px_#0f172a]', 'dark:shadow-[2.5px_2.5px_0px_0px_#334155]', 'bg-slate-50', 'dark:bg-slate-900');
            trigger.setAttribute('aria-expanded', 'true');
            chevron.classList.add('rotate-180', 'text-slate-800');

            const activeItem = optionElements[select.selectedIndex]?.el;
            if (activeItem) {
                const menuScrollTop = menu.scrollTop;
                const menuHeight = menu.clientHeight;
                const itemTop = activeItem.offsetTop;
                const itemHeight = activeItem.offsetHeight;

                if (itemTop < menuScrollTop) {
                    menu.scrollTop = itemTop;
                } else if (itemTop + itemHeight > menuScrollTop + menuHeight) {
                    menu.scrollTop = itemTop + itemHeight - menuHeight;
                }
            }
        }

        function closeMenu() {
            menu.classList.remove('is-open');
            trigger.classList.remove('border-slate-800', 'dark:border-slate-400', 'shadow-[2.5px_2.5px_0px_0px_#0f172a]', 'dark:shadow-[2.5px_2.5px_0px_0px_#334155]', 'bg-slate-50', 'dark:bg-slate-900');
            trigger.setAttribute('aria-expanded', 'false');
            chevron.classList.remove('rotate-180', 'text-slate-800');

            clearTimeout(closeZIndexTimer);
            closeZIndexTimer = setTimeout(() => {
                if (!menu.classList.contains('is-open')) {
                    wrapper.style.zIndex = '';
                }
            }, 180);
        }

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            if (menu.classList.contains('is-open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        select.addEventListener('change', () => {
            updateOptionStates(select.selectedIndex);
        });

        select.addEventListener('invalid', (e) => {
            e.preventDefault();
            trigger.classList.add('border-rose-500', 'ring-2', 'ring-rose-400');
            trigger.focus();
        });

        const form = select.closest('form');
        if (form) {
            form.addEventListener('reset', () => {
                setTimeout(() => {
                    updateOptionStates(select.selectedIndex);
                }, 10);
            });
        }
    });
}

// Global click outside listener to close menus smoothly
document.addEventListener('click', (e) => {
    if (!e.target.closest('.neo-select-wrapper')) {
        document.querySelectorAll('.neo-select-menu.is-open').forEach(menu => {
            menu.classList.remove('is-open');
            const wrapper = menu.closest('.neo-select-wrapper');
            const trigger = wrapper?.querySelector('.neo-select-trigger');
            const chevron = wrapper?.querySelector('.neo-select-chevron');
            if (trigger) {
                trigger.classList.remove('border-slate-800', 'shadow-[2.5px_2.5px_0px_0px_#0f172a]', 'bg-slate-50');
                trigger.setAttribute('aria-expanded', 'false');
            }
            if (chevron) {
                chevron.classList.remove('rotate-180', 'text-slate-800');
            }
            if (wrapper) {
                setTimeout(() => {
                    if (!menu.classList.contains('is-open')) {
                        wrapper.style.zIndex = '';
                    }
                }, 180);
            }
        });
    }
});

// Global Escape key listener to close menus smoothly
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.neo-select-menu.is-open').forEach(menu => {
            menu.classList.remove('is-open');
            const wrapper = menu.closest('.neo-select-wrapper');
            const trigger = wrapper?.querySelector('.neo-select-trigger');
            const chevron = wrapper?.querySelector('.neo-select-chevron');
            if (trigger) {
                trigger.classList.remove('border-slate-800', 'shadow-[2.5px_2.5px_0px_0px_#0f172a]', 'bg-slate-50');
                trigger.setAttribute('aria-expanded', 'false');
            }
            if (chevron) {
                chevron.classList.remove('rotate-180', 'text-slate-800');
            }
            if (wrapper) {
                setTimeout(() => {
                    if (!menu.classList.contains('is-open')) {
                        wrapper.style.zIndex = '';
                    }
                }, 180);
            }
        });
    }
});

let searchInputDebounceTimer = null;

// Live instant search as the user types in text/search inputs inside data-auto-submit forms
document.addEventListener('input', (e) => {
    const input = e.target;
    if (input.matches('input[type="text"], input[type="search"], input:not([type])')) {
        const form = input.closest('form[data-auto-submit]');
        if (form && document.getElementById('filter-results-container')) {
            // Toggle clear button if present in same container
            const container = input.closest('div');
            const clearBtn = container ? container.querySelector('[data-clear-search]') : null;
            if (clearBtn) {
                if (input.value.trim().length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }

            clearTimeout(searchInputDebounceTimer);
            searchInputDebounceTimer = setTimeout(() => {
                handleLiveFilter(form, { replaceState: true });
            }, 250);
        }
    }
});

// Click handler for 1-click clear search button
document.addEventListener('click', (e) => {
    const clearBtn = e.target.closest('[data-clear-search]');
    if (clearBtn) {
        e.preventDefault();
        const container = clearBtn.closest('div');
        const input = container?.querySelector('input[type="text"], input[type="search"], input:not([type])');
        if (input) {
            input.value = '';
            clearBtn.classList.add('hidden');
            input.focus();
            const form = input.closest('form[data-auto-submit]');
            if (form && document.getElementById('filter-results-container')) {
                clearTimeout(searchInputDebounceTimer);
                handleLiveFilter(form, { replaceState: true });
            }
        }
    }
});

// Auto-delegate change events for selects, checkboxes, dates, etc. inside data-auto-submit forms
document.addEventListener('change', (e) => {
    const input = e.target;
    const form = input.closest('form[data-auto-submit]');
    if (form && document.getElementById('filter-results-container')) {
        clearTimeout(searchInputDebounceTimer);
        handleLiveFilter(form);
    }
});

// Intercept form submissions for live-filter forms (e.g. search input on Enter or clicking search button)
document.addEventListener('submit', (e) => {
    const form = e.target.closest('form[data-auto-submit]');
    if (form && document.getElementById('filter-results-container')) {
        e.preventDefault();
        clearTimeout(searchInputDebounceTimer);
        handleLiveFilter(form);
    }
});

// Support browser back/forward buttons with seamless live sync
window.addEventListener('popstate', () => {
    const resultsContainer = document.getElementById('filter-results-container');
    if (!resultsContainer) return;

    resultsContainer.style.transition = 'opacity 180ms ease';
    resultsContainer.style.opacity = '0.7';

    fetch(window.location.href, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newResults = doc.getElementById('filter-results-container');
        if (newResults) {
            resultsContainer.innerHTML = newResults.innerHTML;
            if (window.refreshIcons) window.refreshIcons();

            // Sync form elements back to URL query params
            const params = new URLSearchParams(window.location.search);
            const form = document.querySelector('form[data-auto-submit]');
            if (form) {
                Array.from(form.elements).forEach(el => {
                    if (el.name) {
                        const val = params.get(el.name) || '';
                        if (el.type === 'checkbox') {
                            el.checked = !!params.get(el.name);
                        } else {
                            el.value = val;
                        }
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }
        }
    })
    .finally(() => {
        resultsContainer.style.opacity = '1';
    });
});

// Automatic MutationObserver to initialize any newly mounted selects (e.g. dialogs, modals, dynamic forms)
if (typeof window !== 'undefined' && typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver((mutations) => {
        let hasNewSelect = false;
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (node.nodeType === 1) {
                    if (node.matches && (node.matches('select.neo-select, select.neo-input') || node.querySelector('select.neo-select, select.neo-input'))) {
                        hasNewSelect = true;
                        break;
                    }
                }
            }
            if (hasNewSelect) break;
        }
        if (hasNewSelect) {
            initNeoSelect();
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            observer.observe(document.body, { childList: true, subtree: true });
        });
    } else {
        observer.observe(document.body, { childList: true, subtree: true });
    }
}

