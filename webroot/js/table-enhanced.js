/**
 * Enhanced Table with Horizontal Scroll & AJAX Filters
 * - Mouse drag scroll (desktop)
 * - Touch swipe scroll (mobile)
 * - Real-time AJAX filtering
 */

class EnhancedTable {
    constructor(tableWrapper) {
        this.wrapper = tableWrapper;
        this.table = tableWrapper.querySelector('table');
        this.isDown = false;
        this.startX = 0;
        this.scrollLeft = 0;
        this.filterInputs = [];
        this.filterTimeout = null;
        this.currentFilters = {};

        this.init();
    }

    init() {
        this.setupScrollIndicators();
        this.setupMouseDrag();
        this.setupFilters();
        this.updateScrollIndicators();
    }

    // ===== SCROLL INDICATORS =====
    setupScrollIndicators() {
        // Add scroll indicators
        const leftIndicator = document.createElement('div');
        leftIndicator.className = 'scroll-indicator left';
        leftIndicator.innerHTML = '<i class="fas fa-chevron-left"></i>';

        const rightIndicator = document.createElement('div');
        rightIndicator.className = 'scroll-indicator right';
        rightIndicator.innerHTML = '<i class="fas fa-chevron-right"></i>';

        this.wrapper.appendChild(leftIndicator);
        this.wrapper.appendChild(rightIndicator);

        // Update on scroll
        this.wrapper.addEventListener('scroll', () => this.updateScrollIndicators());
    }

    updateScrollIndicators() {
        const { scrollLeft, scrollWidth, clientWidth } = this.wrapper;

        if (scrollLeft <= 0) {
            this.wrapper.classList.add('at-start');
        } else {
            this.wrapper.classList.remove('at-start');
        }

        if (scrollLeft + clientWidth >= scrollWidth - 1) {
            this.wrapper.classList.add('at-end');
        } else {
            this.wrapper.classList.remove('at-end');
        }
    }

    // ===== MOUSE DRAG SCROLL =====
    setupMouseDrag() {
        // Mouse events
        this.wrapper.addEventListener('mousedown', (e) => this.handleMouseDown(e));
        this.wrapper.addEventListener('mouseleave', () => this.handleMouseUp());
        this.wrapper.addEventListener('mouseup', () => this.handleMouseUp());
        this.wrapper.addEventListener('mousemove', (e) => this.handleMouseMove(e));

        // Touch events
        this.wrapper.addEventListener('touchstart', (e) => this.handleTouchStart(e), { passive: true });
        this.wrapper.addEventListener('touchend', () => this.handleTouchEnd());
        this.wrapper.addEventListener('touchmove', (e) => this.handleTouchMove(e), { passive: true });

        // Prevent text selection while dragging
        this.wrapper.addEventListener('dragstart', (e) => e.preventDefault());
    }

    handleMouseDown(e) {
        // Only activate on right-click or middle-click or when holding space
        if (e.button === 2 || e.button === 1 || e.shiftKey) {
            e.preventDefault();
            this.isDown = true;
            this.wrapper.classList.add('dragging');
            this.startX = e.pageX - this.wrapper.offsetLeft;
            this.scrollLeft = this.wrapper.scrollLeft;
        }
    }

    handleMouseUp() {
        this.isDown = false;
        this.wrapper.classList.remove('dragging');
    }

    handleMouseMove(e) {
        if (!this.isDown) return;
        e.preventDefault();
        const x = e.pageX - this.wrapper.offsetLeft;
        const walk = (x - this.startX) * 2; // Scroll speed multiplier
        this.wrapper.scrollLeft = this.scrollLeft - walk;
    }

    handleTouchStart(e) {
        this.isDown = true;
        this.startX = e.touches[0].pageX - this.wrapper.offsetLeft;
        this.scrollLeft = this.wrapper.scrollLeft;
    }

    handleTouchEnd() {
        this.isDown = false;
    }

    handleTouchMove(e) {
        if (!this.isDown) return;
        const x = e.touches[0].pageX - this.wrapper.offsetLeft;
        const walk = (x - this.startX) * 1.5;
        this.wrapper.scrollLeft = this.scrollLeft - walk;
    }

    // ===== AJAX FILTERS =====
    setupFilters() {
        const filterRow = this.table.querySelector('.filter-row');
        if (!filterRow) return;

        this.filterInputs = filterRow.querySelectorAll('.filter-input');

        this.filterInputs.forEach(input => {
            // Add clear button
            const wrapper = document.createElement('div');
            wrapper.style.position = 'relative';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);

            const clearBtn = document.createElement('button');
            clearBtn.className = 'clear-filter';
            clearBtn.innerHTML = '<i class="fas fa-times"></i>';
            clearBtn.type = 'button';
            clearBtn.addEventListener('click', () => {
                input.value = '';
                input.classList.remove('has-value');
                this.applyFilters();
            });
            wrapper.appendChild(clearBtn);

            // Listen for input
            input.addEventListener('input', (e) => {
                if (e.target.value) {
                    e.target.classList.add('has-value');
                } else {
                    e.target.classList.remove('has-value');
                }

                // Debounce filter
                clearTimeout(this.filterTimeout);
                this.filterTimeout = setTimeout(() => {
                    this.applyFilters();
                }, 500);
            });

            // Clear on ESC
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    input.value = '';
                    input.classList.remove('has-value');
                    this.applyFilters();
                }
            });
        });
    }

    applyFilters() {
        // Collect filter values
        this.currentFilters = {};
        this.filterInputs.forEach(input => {
            const column = input.dataset.column;
            const value = input.value.trim();
            if (value) {
                this.currentFilters[column] = value;

                // Find operator
                // Look in the same container first, or globally in the table
                let operatorInput = input.parentElement.querySelector(`.filter-operator[data-column="${column}"]`);
                if (!operatorInput) {
                    // Try finding in the same td
                    const td = input.closest('td');
                    if (td) {
                        operatorInput = td.querySelector(`.filter-operator[data-column="${column}"]`);
                    }
                }

                if (operatorInput) {
                    this.currentFilters[column + '_operator'] = operatorInput.value;
                }
            }
        });

        // Get current URL and add filters
        const url = new URL(window.location.href);
        const searchParams = new URLSearchParams();

        // Keep existing params (like page, sort)
        url.searchParams.forEach((value, key) => {
            if (!key.startsWith('filter_')) {
                searchParams.set(key, value);
            }
        });

        // Add filter params
        Object.keys(this.currentFilters).forEach(key => {
            searchParams.set(`filter_${key}`, this.currentFilters[key]);
        });

        // Reset to page 1 when filtering
        searchParams.set('page', '1');

        // Reload with filters
        const newUrl = `${url.pathname}?${searchParams.toString()}`;

        // Add loading indicator
        this.wrapper.classList.add('table-loading');

        // Navigate to filtered URL
        window.location.href = newUrl;
    }

    // ===== RESTORE FILTERS FROM URL =====
    restoreFiltersFromUrl() {
        const url = new URL(window.location.href);

        this.filterInputs.forEach(input => {
            const column = input.dataset.column;
            const filterValue = url.searchParams.get(`filter_${column}`);

            if (filterValue) {
                input.value = filterValue;
                input.classList.add('has-value');
            }
        });
    }
}

// ===== AUTO INITIALIZATION =====
document.addEventListener('DOMContentLoaded', () => {
    // Initialize all enhanced tables
    const tableWrappers = document.querySelectorAll('.table-scroll-wrapper');
    tableWrappers.forEach(wrapper => {
        const enhancedTable = new EnhancedTable(wrapper);
        enhancedTable.restoreFiltersFromUrl();
    });

    // Disable right-click context menu on table wrapper
    tableWrappers.forEach(wrapper => {
        wrapper.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            return false;
        });
    });
});

// ===== HELPER: Show delete confirmation =====
function confirmDelete(url, itemName) {
    if (confirm(`Are you sure you want to delete "${itemName}"?\n\nThis action cannot be undone.`)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrfToken"]');
        if (csrfToken) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_csrfToken';
            input.value = csrfToken.content;
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    }
    return false;
}
