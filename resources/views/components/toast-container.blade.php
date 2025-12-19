<!-- Toast Container -->
<div class="toast-container" id="toast-container"></div>

@push('scripts')
<script>
// Toast Notification System
window.showToast = function(message, type = 'info', duration = 3000) {
    const container = document.getElementById('toast-container');
    if (!container) {
        const newContainer = document.createElement('div');
        newContainer.id = 'toast-container';
        newContainer.className = 'toast-container';
        document.body.appendChild(newContainer);
    }

    const toast = document.createElement('div');
    toast.className = `custom-toast ${type}`;

    const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
    };

    toast.innerHTML = `
        <i class="bi ${icons[type] || icons.info} custom-toast-icon"></i>
        <div class="custom-toast-content">
            <div class="custom-toast-title">${type === 'success' ? 'Thành công' : type === 'error' ? 'Lỗi' : type === 'warning' ? 'Cảnh báo' : 'Thông báo'}</div>
            <div class="custom-toast-message">${message}</div>
        </div>
        <button type="button" class="custom-toast-close" onclick="this.closest('.custom-toast').remove()">
            <i class="bi bi-x"></i>
        </button>
    `;

    const finalContainer = document.getElementById('toast-container');
    finalContainer.appendChild(toast);

    // Auto remove after duration
    setTimeout(() => {
        toast.style.animation = 'slideInRight 0.3s ease-out reverse';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 300);
    }, duration);

    return toast;
};
</script>
@endpush

