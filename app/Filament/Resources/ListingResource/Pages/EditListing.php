<?php

namespace App\Filament\Resources\ListingResource\Pages;

use App\Filament\Resources\ListingResource;
use App\Models\ListingImage;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditListing extends EditRecord
{
    protected static string $resource = ListingResource::class;

    protected $thumbnail = null;
    protected $galleryImages = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Load thumbnail và gallery images vào form data
     * Convert giá từ đồng → triệu để hiển thị trong form
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $listing = $this->record;

        if (isset($data['price']) && $data['price'] > 0) {
            $data['price'] = $data['price'] / 1000000;
        }

        if (isset($data['price_per_m2']) && $data['price_per_m2'] > 0) {
            $data['price_per_m2'] = $data['price_per_m2'] / 1000000;
        }

        // Lấy thumbnail (ảnh primary)
        $primaryImage = $listing->primaryImage;
        if ($primaryImage) {
            $data['thumbnail'] = $primaryImage->image_path;
        }

        // Lấy gallery images (các ảnh không phải primary)
        $galleryImages = $listing->images()
            ->where('is_primary', false)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($image) {
                return [
                    'image' => $image->image_path,
                    'sort_order' => $image->sort_order,
                ];
            })
            ->toArray();

        $data['gallery_images'] = $galleryImages;

        return $data;
    }

    /**
     * Xử lý sau khi cập nhật - lưu thumbnail và gallery images
     * Convert giá từ triệu (Form) → đồng (DB) để lưu
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Lưu thumbnail và gallery_images vào biến instance
        $this->thumbnail = $data['thumbnail'] ?? null;
        $this->galleryImages = $data['gallery_images'] ?? [];

        // Xóa khỏi data để không lưu vào listing
        unset($data['thumbnail'], $data['gallery_images']);

        if (isset($data['price']) && $data['price'] > 0) {
            $data['price'] = $data['price'] * 1000000;
        }

        if (isset($data['price_per_m2']) && $data['price_per_m2'] > 0) {
            $data['price_per_m2'] = $data['price_per_m2'] * 1000000;
        } elseif (isset($data['price']) && isset($data['area']) && $data['area'] > 0) {
            // Nếu không có price_per_m2, tính từ price và area
            $data['price_per_m2'] = $data['price'] / $data['area'];
        }

        // Tự động set approved_at khi status = 'approved'
        if (isset($data['status']) && $data['status'] === 'approved') {
            // Nếu đang chuyển từ trạng thái khác sang approved và chưa có approved_at
            if ($this->record->status !== 'approved' || !$this->record->approved_at) {
                $data['approved_at'] = now();
            }
        } elseif (isset($data['status']) && $data['status'] !== 'approved') {
            // Nếu đổi từ approved sang trạng thái khác, xóa approved_at
            $data['approved_at'] = null;
        }

        return $data;
    }

    /**
     * Xử lý sau khi lưu thành công
     */
    protected function afterSave(): void
    {
        $listing = $this->record;

        // Xử lý thumbnail
        // Xóa thumbnail cũ
        $listing->images()->where('is_primary', true)->delete();

        // Tạo thumbnail mới nếu có
        if ($this->thumbnail) {
            ListingImage::create([
                'listing_id' => $listing->id,
                'image_path' => $this->thumbnail,
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        // Xử lý gallery images
        // Xóa các ảnh gallery cũ (không phải primary)
        $listing->images()->where('is_primary', false)->delete();

        // Tạo gallery images mới
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
    }
}
