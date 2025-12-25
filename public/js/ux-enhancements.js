/**
 * UX Enhancements - Improved User Experience
 * Handles lazy loading, smooth interactions, and accessibility
 */

(function() {
    'use strict';

    // 1. Lazy Loading Images
    function initLazyLoading() {
        if ('loading' in HTMLImageElement.prototype) {
            // Native lazy loading supported
            const images = document.querySelectorAll('img[data-src]');
            images.forEach(img => {
                img.src = img.dataset.src;
                img.loading = 'lazy';
                img.addEventListener('load', function() {
                    this.classList.add('loaded');
                });
            });
        } else {
            // Fallback: Intersection Observer
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.add('loaded');
                        img.classList.remove('loading');
                        observer.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                img.classList.add('loading');
                imageObserver.observe(img);
            });
        }
    }

    // 2. Smooth Scroll to Top Button
    function initScrollToTop() {
        // Create button if not exists
        if (!document.getElementById('scroll-to-top')) {
            const btn = document.createElement('button');
            btn.id = 'scroll-to-top';
            btn.className = 'btn btn-primary position-fixed bottom-0 end-0 m-3 rounded-circle shadow-lg';
            btn.style.cssText = 'width: 56px; height: 56px; z-index: 1000; display: none;';
            btn.innerHTML = '<i class="bi bi-arrow-up"></i>';
            btn.setAttribute('aria-label', 'Cuộn lên đầu trang');
            document.body.appendChild(btn);

            // Show/hide button based on scroll position
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    btn.style.display = 'flex';
                    btn.style.alignItems = 'center';
                    btn.style.justifyContent = 'center';
                } else {
                    btn.style.display = 'none';
                }
            });

            // Scroll to top on click
            btn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    }

    // 3. Enhanced Form Validation Feedback
    function initFormValidation() {
        const forms = document.querySelectorAll('form[novalidate]');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Find first invalid field
                    const firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });

                        // Add shake animation
                        firstInvalid.classList.add('shake');
                        setTimeout(() => {
                            firstInvalid.classList.remove('shake');
                        }, 500);
                    }
                }
                form.classList.add('was-validated');
            });
        });
    }

    // 4. Loading Button States
    function initLoadingButtons() {
        document.querySelectorAll('form').forEach(form => {
            // Skip filter forms - they handle their own button states
            if (form.id === 'filter-form' || form.id === 'filter-form-mobile') {
                return;
            }

            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    submitBtn.classList.add('btn-loading');
                    submitBtn.disabled = true;

                    // Re-enable after 10 seconds (safety)
                    setTimeout(() => {
                        submitBtn.classList.remove('btn-loading');
                        submitBtn.disabled = false;
                    }, 10000);
                }
            });
        });
    }
    // 6. Debounce Function for Search
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // 7. Enhanced Search Input
    function initSearchEnhancements() {
        const searchInputs = document.querySelectorAll('input[type="search"], input[name="q"]');
        searchInputs.forEach(input => {
            // Clear button
            const clearBtn = input.parentElement.querySelector('.btn-clear-search');
            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    input.value = '';
                    input.focus();
                    if (input.form) {
                        input.form.submit();
                    }
                });
            }

            // Debounced search suggestions
            const debouncedSearch = debounce(function() {
                // Trigger search suggestions update
                if (window.updateSearchSuggestions) {
                    window.updateSearchSuggestions(input.value);
                }
            }, 300);

            input.addEventListener('input', debouncedSearch);
        });
    }

    // 8. Toast Notification Helper
    window.showToast = function(message, type = 'info', duration = 3000) {
        const toastContainer = document.getElementById('toast-container') || document.body;

        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-x-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi ${icons[type] || icons.info}"></i>
                    <span>${message}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast, { delay: duration });
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', function() {
            toast.remove();
        });
    };

    // 9. Copy to Clipboard Helper
    window.copyToClipboard = function(text, successMessage = 'Đã sao chép!') {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                window.showToast(successMessage, 'success', 2000);
            }).catch(() => {
                window.showToast('Không thể sao chép', 'error');
            });
        } else {
            // Fallback
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                window.showToast(successMessage, 'success', 2000);
            } catch (err) {
                window.showToast('Không thể sao chép', 'error');
            }
            document.body.removeChild(textarea);
        }
    };

    // 10. Page Transition
    function initPageTransitions() {
        document.body.classList.add('page-transition');
    }

    // 11. Focus Management for Modals
    function initModalFocusManagement() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('shown.bs.modal', function() {
                const firstInput = modal.querySelector('input, textarea, select, button');
                if (firstInput) {
                    firstInput.focus();
                }
            });
        });
    }

    // 12. Shake Animation CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        .shake {
            animation: shake 0.5s;
        }
    `;
    document.head.appendChild(style);

    // Initialize all enhancements when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initLazyLoading();
            initScrollToTop();
            initFormValidation();
            initLoadingButtons();
            initSearchEnhancements();
            initPageTransitions();
            initModalFocusManagement();
        });
    } else {
        initLazyLoading();
        initScrollToTop();
        initFormValidation();
        initLoadingButtons();
        initSearchEnhancements();
        initPageTransitions();
        initModalFocusManagement();
    }

    // Re-initialize lazy loading for dynamically added images
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Element node
                    const images = node.querySelectorAll ? node.querySelectorAll('img[data-src]') : [];
                    images.forEach(img => {
                        if ('loading' in HTMLImageElement.prototype) {
                            img.src = img.dataset.src;
                            img.loading = 'lazy';
                        }
                    });
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

})();


