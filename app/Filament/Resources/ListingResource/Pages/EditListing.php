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

        $primaryImage = $listing->primaryImage;
        if ($primaryImage) {
            $data['thumbnail'] = $primaryImage->image_path;
        }

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

        unset($data['thumbnail'], $data['gallery_images']);

        // Lấy giá trị từ form hoặc từ record hiện tại (nếu không thay đổi)
        if (isset($data['price_per_m2']) && $data['price_per_m2'] > 0) {
            // Convert từ triệu/m² → đồng/m²
            $data['price_per_m2'] = $data['price_per_m2'] * 1000000;
        } elseif (!isset($data['price_per_m2']) && $this->record->price_per_m2) {
            // Nếu không có trong form, dùng giá trị hiện tại từ DB
            $data['price_per_m2'] = $this->record->price_per_m2;
        }
        
        // Lấy diện tích từ form hoặc từ record hiện tại
        $area = $data['area'] ?? $this->record->area;
        
        // Tính giá tổng từ đơn giá/m² × diện tích (LUÔN tính lại để đảm bảo đúng)
        if (isset($data['price_per_m2']) && $data['price_per_m2'] > 0 && $area && $area > 0) {
            // Giá tổng = đơn giá/m² × diện tích (cả hai đều tính bằng đồng)
            $data['price'] = $data['price_per_m2'] * $area;
        } elseif (isset($data['price']) && $data['price'] > 0) {
            // Fallback: nếu có nhập giá tổng trực tiếp (triệu) → convert sang đồng
            // Chỉ dùng khi không có price_per_m2 (dữ liệu cũ)
            $data['price'] = $data['price'] * 1000000;
            // Tính lại đơn giá/m² nếu chưa có và có diện tích
            if (!isset($data['price_per_m2']) && $area && $area > 0) {
                $data['price_per_m2'] = $data['price'] / $area;
            }
        }

        if (isset($data['status']) && $data['status'] === 'approved') {
            if ($this->record->status !== 'approved' || !$this->record->approved_at) {
                $data['approved_at'] = now();
            }
        } elseif (isset($data['status']) && $data['status'] !== 'approved') {
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

        $listing->images()->where('is_primary', true)->delete();

        if ($this->thumbnail) {
            ListingImage::create([
                'listing_id' => $listing->id,
                'image_path' => $this->thumbnail,
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        $listing->images()->where('is_primary', false)->delete();

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
