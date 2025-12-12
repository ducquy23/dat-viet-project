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
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->thumbnail = $data['thumbnail'] ?? null;
        $this->galleryImages = $data['gallery_images'] ?? [];
        unset($data['thumbnail'], $data['gallery_images']);

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
