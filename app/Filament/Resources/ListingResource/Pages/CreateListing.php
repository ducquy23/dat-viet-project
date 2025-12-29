<?php

namespace App\Filament\Resources\ListingResource\Pages;

use App\Filament\Resources\ListingResource;
use App\Models\ListingImage;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateListing extends CreateRecord
{
    protected static string $resource = ListingResource::class;

    protected $thumbnail = null;
    protected $galleryImages = [];

    /**
     * Xử lý sau khi tạo listing - lưu thumbnail và gallery images
     * Convert giá từ triệu (Form) → đồng (DB) để lưu
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->thumbnail = $data['thumbnail'] ?? null;
        $this->galleryImages = $data['gallery_images'] ?? [];
        unset($data['thumbnail'], $data['gallery_images']);

        // Convert price_per_m2 từ triệu/m² (Form) → đồng/m² (DB)
        if (!isset($data['price_per_m2']) || $data['price_per_m2'] <= 0) {
            throw new \Exception('Vui lòng nhập đơn giá /m²');
        }
        
        if (!isset($data['area']) || $data['area'] <= 0) {
            throw new \Exception('Vui lòng nhập diện tích');
        }
        
        // Convert đơn giá/m² từ triệu/m² → đồng/m²
        $data['price_per_m2'] = $data['price_per_m2'] * 1000000;
        
        // Tính giá tổng từ đơn giá/m² × diện tích (LUÔN tính để đảm bảo đúng)
        // Giá tổng = đơn giá/m² × diện tích (cả hai đều tính bằng đồng)
        $data['price'] = $data['price_per_m2'] * $data['area'];

        // Tự động set approved_at khi status = 'approved'
        if (isset($data['status']) && $data['status'] === 'approved') {
            $data['approved_at'] = now();
        }

        return $data;
    }

    /**
     * Xử lý sau khi tạo listing thành công
     */
    protected function handleRecordCreation(array $data): Model
    {
        $listing = parent::handleRecordCreation($data);
        if ($this->thumbnail) {
            ListingImage::create([
                'listing_id' => $listing->id,
                'image_path' => $this->thumbnail,
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        // Lưu gallery images
        if (is_array($this->galleryImages) && !empty($this->galleryImages)) {
            foreach ($this->galleryImages as $index => $imageData) {
                if (isset($imageData['image']) && $imageData['image']) {
                    ListingImage::create([
                        'listing_id' => $listing->id,
                        'image_path' => $imageData['image'],
                        'is_primary' => false,
                        'sort_order' => $imageData['sort_order'] ?? $index + 1,
                    ]);
                }
            }
        }

        return $listing;
    }
}
