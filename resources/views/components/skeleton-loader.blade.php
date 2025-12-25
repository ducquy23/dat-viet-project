<!-- Skeleton Loader Component -->
<div class="skeleton-loader" style="display: none;">
    <div class="skeleton skeleton-image"></div>
    <div class="skeleton skeleton-title"></div>
    <div class="skeleton skeleton-text"></div>
    <div class="skeleton skeleton-text" style="width: 80%;"></div>
    <div class="skeleton skeleton-text" style="width: 60%;"></div>
</div>

@push('styles')
<style>
.skeleton-loader {
    padding: 16px;
    background: white;
    border-radius: 12px;
    margin-bottom: 16px;
}

.skeleton-loader.listing-card {
    min-height: 300px;
}

.skeleton-loader.detail-panel {
    min-height: 400px;
}
</style>
@endpush




