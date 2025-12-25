@php
    $images = $getState() ?? [];
    $recordId = $getRecord()->id;
@endphp
<div>
    @if(count($images) > 0)
        <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem;">
            @foreach($images as $image)
                <div>
                    <img 
                        src="{{ asset('storage/' . $image['url']) }}" 
                        alt="Gallery image"
                        style="width: 100%; height: 12rem; object-fit: cover; border-radius: 0.5rem; cursor: pointer;"
                        onclick="(function(url){var img=document.createElement('img');img.src=url;img.style='max-width:90vw;max-height:90vh;border-radius:0.5rem;';var div=document.createElement('div');div.style='position:fixed;inset:0;background:rgba(0,0,0,0.75);z-index:9999;display:flex;align-items:center;justify-content:center;cursor:pointer';div.onclick=function(){document.body.removeChild(div);};div.appendChild(img);document.body.appendChild(div);})('{{ asset('storage/' . ($image['full_url'] ?? $image['url'])) }}')"
                    />
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">Chưa có ảnh gallery</p>
    @endif
</div>

