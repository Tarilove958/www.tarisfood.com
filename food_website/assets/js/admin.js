/* ===========================
   ADMIN DASHBOARD JAVASCRIPT
   Modern Admin Controls
   =========================== */

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', function() {
    initAdminSidebar();
    initAdminAlerts();
    initAdminForms();
    initDeleteConfirmation();
    initFloatingActionMenu();
    initTableFeatures();
    initDataFilters();
});

// ===== ADMIN SIDEBAR =====
function initAdminSidebar() {
    const sidebarToggle = document.querySelector('.sidebar-toggle, [data-toggle="sidebar"]');
    const sidebar = document.querySelector('.admin-sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : 'auto';
        });

        // Close sidebar on link click (mobile)
        sidebar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        });

        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.admin-sidebar') && !e.target.closest('.sidebar-toggle')) {
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('active');
                }
            }
        });
    }

    // Mark active menu item
    const currentPage = window.location.pathname;
    sidebar?.querySelectorAll('a').forEach(link => {
        const href = link.getAttribute('href');
        if (href && (currentPage.includes(href) || (href === 'index.php' && currentPage.endsWith('admin/')))) {
            link.classList.add('active');
        }
    });
}

// ===== ADMIN ALERTS =====
function initAdminAlerts() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        // Auto-dismiss success and info alerts
        if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
            setTimeout(() => {
                dismissAlert(alert);
            }, 5000);
        }

        // Close button handler
        const closeBtn = alert.querySelector('.alert-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                dismissAlert(alert);
            });
        }
    });
}

function dismissAlert(alert) {
    alert.style.animation = 'slideUp 0.3s ease forwards';
    setTimeout(() => alert.remove(), 300);
}

// ===== ADMIN FORMS =====
function initAdminForms() {
    const forms = document.querySelectorAll('form[data-validate="true"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateAdminForm(this)) {
                e.preventDefault();
            }
        });

        // Real-time field validation
        const fields = form.querySelectorAll('input[required], select[required], textarea[required]');
        fields.forEach(field => {
            field.addEventListener('blur', function() {
                validateAdminField(this);
            });
        });
    });
}

function validateAdminForm(form) {
    let isValid = true;
    const fields = form.querySelectorAll('input[required], select[required], textarea[required]');

    fields.forEach(field => {
        if (!validateAdminField(field)) {
            isValid = false;
        }
    });

    return isValid;
}

function validateAdminField(field) {
    let isValid = true;
    const value = field.value.trim();

    // Basic validation
    if (value === '') {
        isValid = false;
    }

    // Email validation
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        isValid = emailRegex.test(value);
    }

    // Number validation
    if (field.type === 'number' && value) {
        isValid = !isNaN(parseFloat(value)) && isFinite(value);
    }

    // Update field styling
    const formGroup = field.closest('.form-group');
    if (formGroup) {
        if (isValid) {
            formGroup.classList.remove('error');
            formGroup.classList.add('success');
        } else {
            formGroup.classList.add('error');
            formGroup.classList.remove('success');
        }
    }

    return isValid;
}

// ===== DELETE CONFIRMATION =====
function initDeleteConfirmation() {
    const deleteButtons = document.querySelectorAll('[data-action="delete"], .action-btn-delete');
    
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const itemName = this.getAttribute('data-item') || 'this item';
            
            if (confirm(`Are you sure you want to delete ${itemName}? This action cannot be undone.`)) {
                if (this.href) {
                    window.location.href = this.href;
                } else if (this.form) {
                    this.form.submit();
                }
            }
        });
    });
}

// ===== FLOATING ACTION MENU =====
function initFloatingActionMenu() {
    const floatingBtn = document.querySelector('.floating-btn');
    const floatingMenu = document.querySelector('.floating-menu');

    if (floatingBtn && floatingMenu) {
        floatingBtn.addEventListener('click', function() {
            floatingMenu.classList.toggle('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.floating-action-menu')) {
                floatingMenu.classList.remove('active');
            }
        });
    }
}

// ===== TABLE FEATURES =====
function initTableFeatures() {
    // Row selection
    const selectAllCheckbox = document.querySelector('input[name="select-all"]');
    const rowCheckboxes = document.querySelectorAll('input[name="row-select"]');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                updateRowSelection();
            });
        });
    }

    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateRowSelection();
            updateSelectAllCheckbox();
        });
    });

    // Row hover effects
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(59, 130, 246, 0.05)';
        });
        row.addEventListener('mouseleave', function() {
            if (!this.classList.contains('selected')) {
                this.style.backgroundColor = '';
            }
        });
    });
}

function updateRowSelection() {
    const selectedRows = document.querySelectorAll('input[name="row-select"]:checked');
    const bulkActions = document.querySelector('.bulk-actions');
    
    if (bulkActions) {
        if (selectedRows.length > 0) {
            bulkActions.style.display = 'block';
            document.querySelector('[data-count]')?.setAttribute('data-count', selectedRows.length);
        } else {
            bulkActions.style.display = 'none';
        }
    }
}

function updateSelectAllCheckbox() {
    const selectAllCheckbox = document.querySelector('input[name="select-all"]');
    const rowCheckboxes = document.querySelectorAll('input[name="row-select"]');
    const checkedCount = document.querySelectorAll('input[name="row-select"]:checked').length;

    if (selectAllCheckbox) {
        selectAllCheckbox.checked = checkedCount === rowCheckboxes.length && rowCheckboxes.length > 0;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
    }
}

// ===== DATA FILTERS =====
function initDataFilters() {
    const filterInputs = document.querySelectorAll('[data-filter]');
    
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            applyFilters();
        });
    });
}

function applyFilters() {
    const filters = {};
    document.querySelectorAll('[data-filter]').forEach(input => {
        filters[input.getAttribute('data-filter')] = input.value;
    });

    // Send filter request or apply client-side filtering
    const table = document.querySelector('table');
    if (table) {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            let visible = true;
            for (let [key, value] of Object.entries(filters)) {
                if (value && !row.textContent.toLowerCase().includes(value.toLowerCase())) {
                    visible = false;
                    break;
                }
            }
            row.style.display = visible ? '' : 'none';
        });
    }
}

// ===== SEARCH FUNCTIONALITY =====
window.initSearch = function(tableSelector) {
    const searchInput = document.querySelector('[data-search]');
    if (!searchInput) return;

    searchInput.addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        const table = document.querySelector(tableSelector);
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
};

// ===== BULK ACTIONS =====
window.bulkAction = function(action) {
    const selected = document.querySelectorAll('input[name="row-select"]:checked');
    
    if (selected.length === 0) {
        showNotification('Please select items', 'warning');
        return;
    }

    if (action === 'delete') {
        if (!confirm(`Delete ${selected.length} item(s)? This action cannot be undone.`)) {
            return;
        }
    }

    const ids = Array.from(selected).map(cb => cb.value);
    
    // Send bulk action request
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="${action}">
        <input type="hidden" name="ids" value="${ids.join(',')}">
    `;
    document.body.appendChild(form);
    form.submit();
};

// ===== EXPORT DATA =====
window.exportData = function(format = 'csv') {
    const table = document.querySelector('table');
    if (!table) return;

    let data = '';
    
    if (format === 'csv') {
        // Export as CSV
        const rows = table.querySelectorAll('tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td, th');
            const rowData = Array.from(cells).map(cell => `"${cell.textContent.trim()}"`).join(',');
            data += rowData + '\n';
        });

        downloadFile(data, 'table.csv', 'text/csv');
    } else if (format === 'json') {
        // Export as JSON
        const headers = Array.from(table.querySelectorAll('thead th')).map(h => h.textContent.trim());
        const rows = table.querySelectorAll('tbody tr');
        const jsonData = Array.from(rows).map(row => {
            const cells = row.querySelectorAll('td');
            const obj = {};
            headers.forEach((header, index) => {
                obj[header] = cells[index]?.textContent.trim();
            });
            return obj;
        });

        data = JSON.stringify(jsonData, null, 2);
        downloadFile(data, 'table.json', 'application/json');
    }
};

function downloadFile(data, filename, type) {
    const blob = new Blob([data], { type });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

// ===== STATS ANIMATION =====
window.animateStats = function() {
    const statValues = document.querySelectorAll('[data-stat-value]');
    
    statValues.forEach(stat => {
        const target = parseInt(stat.getAttribute('data-stat-value'));
        const duration = 1500;
        const increment = target / (duration / 16);
        let current = 0;

        const interval = setInterval(() => {
            current += increment;
            if (current >= target) {
                stat.textContent = target;
                clearInterval(interval);
            } else {
                stat.textContent = Math.floor(current);
            }
        }, 16);
    });
};

// Run on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        animateStats();
    });
} else {
    animateStats();
}

// ===== MODAL DIALOGS =====
window.openAdminModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
};

window.closeAdminModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
};

// Close modal on outside click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
});

// ===== DATE RANGE PICKER =====
window.initDateRange = function() {
    const startDateInput = document.querySelector('[data-date-start]');
    const endDateInput = document.querySelector('[data-date-end]');

    if (startDateInput && endDateInput) {
        const today = new Date().toISOString().split('T')[0];
        startDateInput.setAttribute('max', today);
        endDateInput.setAttribute('max', today);

        const applyBtn = document.querySelector('[data-apply-date-range]');
        if (applyBtn) {
            applyBtn.addEventListener('click', function() {
                const start = startDateInput.value;
                const end = endDateInput.value;

                if (start && end) {
                    if (new Date(start) > new Date(end)) {
                        showNotification('Start date must be before end date', 'error');
                        return;
                    }
                    applyDateFilter(start, end);
                }
            });
        }
    }
};

function applyDateFilter(start, end) {
    // Apply date filter to table
    const table = document.querySelector('table');
    if (!table) return;

    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const dateCell = row.querySelector('[data-date]');
        if (dateCell) {
            const rowDate = dateCell.getAttribute('data-date');
            const isVisible = rowDate >= start && rowDate <= end;
            row.style.display = isVisible ? '' : 'none';
        }
    });
}

// ===== CONSOLE MESSAGE =====
console.log('%c🍽️ Welcome to FoodHub Admin Dashboard!', 'color: #f97316; font-size: 20px; font-weight: bold;');
console.log('%cManaging your food business like a pro', 'color: #3b82f6; font-size: 14px;');

