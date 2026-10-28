/**
 * Bespoke Soft Neo-Brutalism Date & Time Picker
 * Supports:
 * - Date-Only mode (e.g. Buku Kas filter) with compact calendar popover & instant auto-submit
 * - Date & Time mode (e.g. Order Design deadline) with side-by-side calendar + time columns
 */

const MONTH_NAMES = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

export function initNeoDatePicker() {
    const containers = document.querySelectorAll('[data-neo-datepicker-container]');

    containers.forEach(container => {
        if (container._neoDatepickerInit) return;
        container._neoDatepickerInit = true;

        const hiddenInput = container.querySelector('input[type="hidden"]');
        const toggleBtn = container.querySelector('[data-datepicker-toggle]');
        const displayText = container.querySelector('[data-datepicker-text]');
        const popover = container.querySelector('[data-datepicker-popover]');

        if (!hiddenInput || !popover || !toggleBtn) return;

        const isDateOnly = container.dataset.mode === 'date' || (!popover.querySelector('[data-cal-hours]'));
        const placeholder = container.dataset.placeholder || (isDateOnly ? 'Pilih Tanggal...' : 'Pilih Tanggal & Waktu...');

        const monthLabel = popover.querySelector('[data-cal-month]');
        const yearLabel = popover.querySelector('[data-cal-year]');
        const prevBtn = popover.querySelector('[data-cal-prev]');
        const nextBtn = popover.querySelector('[data-cal-next]');
        const daysGrid = popover.querySelector('[data-cal-days]');
        const todayBtn = popover.querySelector('[data-cal-today]');
        const resetBtn = popover.querySelector('[data-cal-reset]');
        const hoursContainer = popover.querySelector('[data-cal-hours]');
        const minutesContainer = popover.querySelector('[data-cal-minutes]');
        const applyBtn = popover.querySelector('[data-cal-apply]');

        // Parse initial value
        let currentDate = new Date();
        let hasValue = false;

        if (hiddenInput.value && hiddenInput.value.trim() !== '') {
            const parsed = new Date(hiddenInput.value.replace(' ', 'T'));
            if (!isNaN(parsed.getTime())) {
                currentDate = parsed;
                hasValue = true;
            }
        } else if (!isDateOnly) {
            // For datetime (deadline), default to tomorrow 17:00
            currentDate.setDate(currentDate.getDate() + 1);
            currentDate.setHours(17, 0, 0, 0);
            hasValue = true;
        }

        let selectedYear = currentDate.getFullYear();
        let selectedMonth = currentDate.getMonth();
        let selectedDay = hasValue ? currentDate.getDate() : null;
        let selectedHour = currentDate.getHours();
        let selectedMinute = currentDate.getMinutes();

        // Round initial minute to nearest 5
        selectedMinute = Math.round(selectedMinute / 5) * 5;
        if (selectedMinute >= 60) selectedMinute = 55;

        let viewYear = selectedYear;
        let viewMonth = selectedMonth;

        function formatDisplayDate(d, m, y) {
            const dayStr = String(d).padStart(2, '0');
            const monthStr = String(m + 1).padStart(2, '0');
            const yearStr = String(y);
            return `${dayStr}/${monthStr}/${yearStr}`;
        }

        function formatDisplayDateTime(d, m, y, hr, min) {
            const dayStr = String(d).padStart(2, '0');
            const monthStr = String(m + 1).padStart(2, '0');
            const yearStr = String(y);
            const hourStr = String(hr).padStart(2, '0');
            const minStr = String(min).padStart(2, '0');
            return `${dayStr}/${monthStr}/${yearStr} ${hourStr}.${minStr}`;
        }

        function formatHiddenDate(y, m, d) {
            const yearStr = String(y);
            const monthStr = String(m + 1).padStart(2, '0');
            const dayStr = String(d).padStart(2, '0');
            return `${yearStr}-${monthStr}-${dayStr}`;
        }

        function formatHiddenDateTime(y, m, d, hr, min) {
            const yearStr = String(y);
            const monthStr = String(m + 1).padStart(2, '0');
            const dayStr = String(d).padStart(2, '0');
            const hourStr = String(hr).padStart(2, '0');
            const minStr = String(min).padStart(2, '0');
            return `${yearStr}-${monthStr}-${dayStr} ${hourStr}:${minStr}`;
        }

        function updateInputs(triggerChange = true) {
            if (isDateOnly) {
                if (!hasValue || selectedDay === null) {
                    hiddenInput.value = '';
                    if (displayText) {
                        displayText.textContent = placeholder;
                        displayText.classList.remove('text-slate-900', 'font-extrabold');
                        displayText.classList.add('text-slate-400', 'font-medium');
                    }
                } else {
                    hiddenInput.value = formatHiddenDate(selectedYear, selectedMonth, selectedDay);
                    if (displayText) {
                        displayText.textContent = formatDisplayDate(selectedDay, selectedMonth, selectedYear);
                        displayText.classList.remove('text-slate-400', 'font-medium');
                        displayText.classList.add('text-slate-900', 'font-extrabold');
                    }
                }
            } else {
                hiddenInput.value = formatHiddenDateTime(selectedYear, selectedMonth, selectedDay, selectedHour, selectedMinute);
                if (displayText) {
                    displayText.textContent = formatDisplayDateTime(selectedDay, selectedMonth, selectedYear, selectedHour, selectedMinute);
                    displayText.classList.remove('text-slate-400', 'font-medium');
                    displayText.classList.add('text-slate-900', 'font-extrabold');
                }
            }

            if (triggerChange) {
                const event = new CustomEvent('change', { bubbles: true, detail: { fromDatePicker: true } });
                hiddenInput.dispatchEvent(event);
            }
        }

        function renderCalendar() {
            if (monthLabel) monthLabel.textContent = MONTH_NAMES[viewMonth];
            if (yearLabel) yearLabel.textContent = viewYear;

            if (!daysGrid) return;
            daysGrid.innerHTML = '';

            const firstDayIndex = new Date(viewYear, viewMonth, 1).getDay(); // 0 is Sunday
            const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
            const daysInPrevMonth = new Date(viewYear, viewMonth, 0).getDate();

            const today = new Date();
            const isCurrentMonthView = today.getFullYear() === viewYear && today.getMonth() === viewMonth;
            const todayDate = today.getDate();

            // Prev month trailing days
            for (let i = firstDayIndex - 1; i >= 0; i--) {
                const dayNum = daysInPrevMonth - i;
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-7 h-7 rounded-lg text-xs font-semibold text-slate-300 hover:bg-slate-50 transition-all flex items-center justify-center cursor-pointer';
                btn.textContent = dayNum;
                btn.addEventListener('click', () => {
                    viewMonth--;
                    if (viewMonth < 0) {
                        viewMonth = 11;
                        viewYear--;
                    }
                    selectedYear = viewYear;
                    selectedMonth = viewMonth;
                    selectedDay = dayNum;
                    hasValue = true;
                    updateInputs(isDateOnly);
                    renderCalendar();
                    if (isDateOnly) {
                        closePopover();
                    }
                });
                daysGrid.appendChild(btn);
            }

            // Current month days
            for (let day = 1; day <= daysInMonth; day++) {
                const btn = document.createElement('button');
                btn.type = 'button';

                const isSelected = hasValue && selectedYear === viewYear && selectedMonth === viewMonth && selectedDay === day;
                const isToday = isCurrentMonthView && todayDate === day;

                let classes = 'w-7 h-7 rounded-lg text-xs flex items-center justify-center transition-all cursor-pointer ';
                if (isSelected) {
                    classes += 'bg-amber-300 text-slate-950 font-black border-[1.5px] border-slate-900 shadow-[1px_1px_0px_0px_#0f172a]';
                } else if (isToday) {
                    classes += 'bg-slate-100 border-[1.5px] border-slate-700 text-slate-950 font-black hover:bg-amber-100';
                } else {
                    classes += 'font-bold text-slate-700 hover:bg-amber-100 hover:text-slate-950';
                }

                btn.className = classes;
                btn.textContent = day;

                btn.addEventListener('click', () => {
                    selectedYear = viewYear;
                    selectedMonth = viewMonth;
                    selectedDay = day;
                    hasValue = true;
                    updateInputs(isDateOnly);
                    renderCalendar();
                    if (isDateOnly) {
                        closePopover();
                    }
                });

                daysGrid.appendChild(btn);
            }

            // Next month leading days (Exact 42 cells total: 6 rows x 7 cols)
            const totalRendered = firstDayIndex + daysInMonth;
            const remaining = 42 - totalRendered;
            for (let day = 1; day <= remaining; day++) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-7 h-7 rounded-lg text-xs font-semibold text-slate-300 hover:bg-slate-50 transition-all flex items-center justify-center cursor-pointer';
                btn.textContent = day;
                btn.addEventListener('click', () => {
                    viewMonth++;
                    if (viewMonth > 11) {
                        viewMonth = 0;
                        viewYear++;
                    }
                    selectedYear = viewYear;
                    selectedMonth = viewMonth;
                    selectedDay = day;
                    hasValue = true;
                    updateInputs(isDateOnly);
                    renderCalendar();
                    if (isDateOnly) {
                        closePopover();
                    }
                });
                daysGrid.appendChild(btn);
            }
        }

        function renderTimeColumns() {
            if (!hoursContainer || !minutesContainer) return;

            // Render Hours (08..23)
            hoursContainer.innerHTML = '';
            for (let h = 8; h <= 23; h++) {
                const btn = document.createElement('button');
                btn.type = 'button';
                const isSelected = selectedHour === h;
                btn.className = `w-11 h-7 rounded-md text-center font-mono text-xs transition-all cursor-pointer flex items-center justify-center shrink-0 ${
                    isSelected 
                        ? 'bg-amber-300 text-slate-950 font-black border-[1.5px] border-slate-900 shadow-[1px_1px_0px_0px_#0f172a]' 
                        : 'font-bold text-slate-700 hover:bg-amber-100 hover:text-slate-950'
                }`;
                btn.textContent = String(h).padStart(2, '0');
                btn.addEventListener('click', () => {
                    selectedHour = h;
                    updateInputs(false);
                    renderTimeColumns();
                });
                hoursContainer.appendChild(btn);
            }

            // Render Minutes in steps of 5 (00..55)
            minutesContainer.innerHTML = '';
            for (let m = 0; m < 60; m += 5) {
                const btn = document.createElement('button');
                btn.type = 'button';
                const isSelected = selectedMinute === m;
                btn.className = `w-11 h-7 rounded-md text-center font-mono text-xs transition-all cursor-pointer flex items-center justify-center shrink-0 ${
                    isSelected 
                        ? 'bg-amber-300 text-slate-950 font-black border-[1.5px] border-slate-900 shadow-[1px_1px_0px_0px_#0f172a]' 
                        : 'font-bold text-slate-700 hover:bg-amber-100 hover:text-slate-950'
                }`;
                btn.textContent = String(m).padStart(2, '0');
                btn.addEventListener('click', () => {
                    selectedMinute = m;
                    updateInputs(false);
                    renderTimeColumns();
                });
                minutesContainer.appendChild(btn);
            }

            // Scroll selected items into view gently
            setTimeout(() => {
                const activeHour = hoursContainer.querySelector('.bg-amber-300');
                if (activeHour) activeHour.scrollIntoView({ block: 'center' });
                const activeMin = minutesContainer.querySelector('.bg-amber-300');
                if (activeMin) activeMin.scrollIntoView({ block: 'center' });
            }, 10);
        }

        function openPopover() {
            // Close any other open popovers
            document.querySelectorAll('[data-datepicker-popover]:not(.hidden)').forEach(p => {
                if (p !== popover) p.classList.add('hidden');
            });

            popover.classList.remove('hidden');
            container.style.zIndex = '50';

            if (hasValue && selectedYear && selectedMonth !== null) {
                viewYear = selectedYear;
                viewMonth = selectedMonth;
            } else {
                const now = new Date();
                viewYear = now.getFullYear();
                viewMonth = now.getMonth();
            }

            renderCalendar();
            if (!isDateOnly) {
                renderTimeColumns();
            }

            // Viewport boundary guard for small mobile screens (prevent edge clipping)
            requestAnimationFrame(() => {
                const rect = popover.getBoundingClientRect();
                if (rect.right > window.innerWidth - 8) {
                    const diff = rect.right - (window.innerWidth - 8);
                    const currentLeft = parseFloat(getComputedStyle(popover).left) || 0;
                    popover.style.left = `${currentLeft - diff}px`;
                }
            });
        }

        function closePopover() {
            popover.classList.add('hidden');
            popover.style.left = '';
            container.style.zIndex = '';
        }

        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (popover.classList.contains('hidden')) {
                openPopover();
            } else {
                closePopover();
            }
        });

        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                viewMonth--;
                if (viewMonth < 0) {
                    viewMonth = 11;
                    viewYear--;
                }
                renderCalendar();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                viewMonth++;
                if (viewMonth > 11) {
                    viewMonth = 0;
                    viewYear++;
                }
                renderCalendar();
            });
        }

        if (todayBtn) {
            todayBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const now = new Date();
                selectedYear = now.getFullYear();
                selectedMonth = now.getMonth();
                selectedDay = now.getDate();
                viewYear = selectedYear;
                viewMonth = selectedMonth;
                hasValue = true;
                updateInputs(isDateOnly);
                renderCalendar();
                if (isDateOnly) {
                    closePopover();
                }
            });
        }

        if (resetBtn) {
            resetBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (isDateOnly) {
                    hasValue = false;
                    selectedDay = null;
                    updateInputs(true);
                    renderCalendar();
                    closePopover();
                } else {
                    // Set to tomorrow 17:00
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    selectedYear = tomorrow.getFullYear();
                    selectedMonth = tomorrow.getMonth();
                    selectedDay = tomorrow.getDate();
                    selectedHour = 17;
                    selectedMinute = 0;
                    viewYear = selectedYear;
                    viewMonth = selectedMonth;
                    hasValue = true;
                    updateInputs(false);
                    renderCalendar();
                    renderTimeColumns();
                }
            });
        }

        if (applyBtn) {
            applyBtn.addEventListener('click', (e) => {
                e.preventDefault();
                updateInputs(true);
                closePopover();
            });
        }

        // External change listener (e.g. from history back/forward or reset filter)
        hiddenInput.addEventListener('change', (e) => {
            if (e.detail && e.detail.fromDatePicker) return;
            if (hiddenInput.value && hiddenInput.value.trim() !== '') {
                const parsed = new Date(hiddenInput.value.replace(' ', 'T'));
                if (!isNaN(parsed.getTime())) {
                    selectedYear = parsed.getFullYear();
                    selectedMonth = parsed.getMonth();
                    selectedDay = parsed.getDate();
                    hasValue = true;
                    if (displayText) {
                        displayText.textContent = isDateOnly 
                            ? formatDisplayDate(selectedDay, selectedMonth, selectedYear)
                            : formatDisplayDateTime(selectedDay, selectedMonth, selectedYear, selectedHour, selectedMinute);
                        displayText.classList.remove('text-slate-400', 'font-medium');
                        displayText.classList.add('text-slate-900', 'font-extrabold');
                    }
                }
            } else if (isDateOnly) {
                hasValue = false;
                selectedDay = null;
                if (displayText) {
                    displayText.textContent = placeholder;
                    displayText.classList.remove('text-slate-900', 'font-extrabold');
                    displayText.classList.add('text-slate-400', 'font-medium');
                }
            }
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) {
                closePopover();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !popover.classList.contains('hidden')) {
                closePopover();
            }
        });

        // Initialize display text on load without triggering form submit
        updateInputs(false);
    });
}
