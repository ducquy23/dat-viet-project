@php
    $data = $getState();
    $latitude = $data['latitude'] ?? null;
    $longitude = $data['longitude'] ?? null;
    $address = $data['address'] ?? '';
    $title = $data['title'] ?? '';
@endphp
<div>
    @if($latitude && $longitude)
        <div style="width: 100%; height: 400px; border-radius: 8px; overflow: hidden;">
            <iframe 
                width="100%" 
                height="100%" 
                style="border:0" 
                loading="lazy" 
                allowfullscreen
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google.maps_key', env('GOOGLE_MAPS_API_KEY', '')) }}&q={{ $latitude }},{{ $longitude }}&zoom=15">
            </iframe>
        </div>
        <div style="margin-top: 8px;">
            <a href="https://www.google.com/maps?q={{ $latitude }},{{ $longitude }}" target="_blank" style="color: #3b82f6; text-decoration: underline;">
                Xem trên Google Maps
            </a>
        </div>
    @else
        <p class="text-muted">Không có thông tin vị trí</p>
    @endif
</div>

