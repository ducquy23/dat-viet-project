@php
    $record = $getRecord();
    $lat = $record?->latitude ?? 21.0285; // Default to Ho Chi Minh City
    $lng = $record?->longitude ?? 105.8542; // Default to Ho Chi Minh City
    $id = uniqid('map_');
@endphp

<div x-data="leafletMap{{ $id }}({{ $lat }}, {{ $lng }})" 
     x-init="initMap()"
     class="leaflet-map-container"
     style="width: 100%; height: 400px; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; margin-bottom: 1rem;">
    <div id="map-{{ $id }}" style="width: 100%; height: 100%;"></div>
    <div class="mt-2 text-sm text-gray-600">
        <p>💡 Click trên bản đồ hoặc kéo marker để cập nhật vị trí</p>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
function leafletMap{{ $id }}(initialLat, initialLng) {
    return {
        map: null,
        marker: null,
        lat: initialLat,
        lng: initialLng,
        initialized: false,
        
        initMap() {
            // Wait a bit for DOM to be ready
            this.$nextTick(() => {
                setTimeout(() => {
                    if (typeof L !== 'undefined') {
                        this.setupMap();
                    } else {
                        // Retry after a short delay
                        setTimeout(() => this.initMap(), 100);
                    }
                }, 100);
            });
        },
        
        setupMap() {
            if (this.initialized) return;
            
            const mapId = 'map-{{ $id }}';
            const mapElement = document.getElementById(mapId);
            if (!mapElement) {
                setTimeout(() => this.setupMap(), 100);
                return;
            }
            
            this.initialized = true;
            
            // Initialize map
            this.map = L.map(mapId).setView([this.lat, this.lng], 13);
            
            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);
            
            // Add marker
            this.marker = L.marker([this.lat, this.lng], {
                draggable: true
            }).addTo(this.map);
            
            // Update coordinates when marker is dragged
            this.marker.on('dragend', (e) => {
                const position = e.target.getLatLng();
                this.updateCoordinates(position.lat, position.lng);
            });
            
            // Update coordinates when map is clicked
            this.map.on('click', (e) => {
                this.marker.setLatLng(e.latlng);
                this.updateCoordinates(e.latlng.lat, e.latlng.lng);
            });
            
            // Watch for changes in latitude/longitude inputs
            this.watchInputs();
        },
        
        updateCoordinates(lat, lng) {
            this.lat = lat;
            this.lng = lng;
            
            // Find inputs - try multiple selectors
            const latInput = document.querySelector('input[id*="latitude"], input[name*="latitude"], input[wire\\:model*="latitude"]') ||
                           Array.from(document.querySelectorAll('input[type="number"]')).find(el => 
                               el.closest('[wire\\:id]') && el.getAttribute('wire:model')?.includes('latitude')
                           );
            
            const lngInput = document.querySelector('input[id*="longitude"], input[name*="longitude"], input[wire\\:model*="longitude"]') ||
                           Array.from(document.querySelectorAll('input[type="number"]')).find(el => 
                               el.closest('[wire\\:id]') && el.getAttribute('wire:model')?.includes('longitude')
                           );
            
            if (latInput) {
                latInput.value = lat.toFixed(8);
                // Trigger Livewire update
                const wireModel = latInput.getAttribute('wire:model');
                if (wireModel && window.Livewire) {
                    const component = latInput.closest('[wire\\:id]');
                    if (component) {
                        const wireId = component.getAttribute('wire:id');
                        if (wireId && window.Livewire.find) {
                            const livewireComponent = window.Livewire.find(wireId);
                            if (livewireComponent) {
                                livewireComponent.set(wireModel, lat.toFixed(8));
                            }
                        }
                    }
                }
                // Trigger events for Filament
                latInput.dispatchEvent(new Event('input', { bubbles: true }));
                latInput.dispatchEvent(new Event('change', { bubbles: true }));
                latInput.dispatchEvent(new Event('blur', { bubbles: true }));
            }
            
            if (lngInput) {
                lngInput.value = lng.toFixed(8);
                // Trigger Livewire update
                const wireModel = lngInput.getAttribute('wire:model');
                if (wireModel && window.Livewire) {
                    const component = lngInput.closest('[wire\\:id]');
                    if (component) {
                        const wireId = component.getAttribute('wire:id');
                        if (wireId && window.Livewire.find) {
                            const livewireComponent = window.Livewire.find(wireId);
                            if (livewireComponent) {
                                livewireComponent.set(wireModel, lng.toFixed(8));
                            }
                        }
                    }
                }
                // Trigger events for Filament
                lngInput.dispatchEvent(new Event('input', { bubbles: true }));
                lngInput.dispatchEvent(new Event('change', { bubbles: true }));
                lngInput.dispatchEvent(new Event('blur', { bubbles: true }));
            }
        },
        
        watchInputs() {
            // Watch for manual input changes
            const checkInputs = () => {
                const latInput = document.querySelector('input[id*="latitude"], input[name*="latitude"], input[wire\\:model*="latitude"]') ||
                               Array.from(document.querySelectorAll('input[type="number"]')).find(el => 
                                   el.closest('[wire\\:id]') && el.getAttribute('wire:model')?.includes('latitude')
                               );
                
                const lngInput = document.querySelector('input[id*="longitude"], input[name*="longitude"], input[wire\\:model*="longitude"]') ||
                               Array.from(document.querySelectorAll('input[type="number"]')).find(el => 
                                   el.closest('[wire\\:id]') && el.getAttribute('wire:model')?.includes('longitude')
                               );
                
                if (latInput && lngInput && this.map && this.marker) {
                    const updateFromInputs = () => {
                        const lat = parseFloat(latInput.value);
                        const lng = parseFloat(lngInput.value);
                        if (!isNaN(lat) && !isNaN(lng) && (Math.abs(lat - this.lat) > 0.0001 || Math.abs(lng - this.lng) > 0.0001)) {
                            this.lat = lat;
                            this.lng = lng;
                            this.map.setView([lat, lng], this.map.getZoom());
                            this.marker.setLatLng([lat, lng]);
                        }
                    };
                    
                    // Remove old listeners to avoid duplicates
                    latInput.removeEventListener('change', updateFromInputs);
                    lngInput.removeEventListener('change', updateFromInputs);
                    
                    // Add new listeners
                    latInput.addEventListener('change', updateFromInputs);
                    lngInput.addEventListener('change', updateFromInputs);
                }
            };
            
            // Check immediately
            setTimeout(checkInputs, 500);
            
            // Watch for DOM changes
            const observer = new MutationObserver(checkInputs);
            observer.observe(document.body, { childList: true, subtree: true });
        }
    }
}
</script>
@endpush

