<?php

namespace App\Filament\Resources\ListingResource\Pages;

use App\Filament\Resources\ListingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListListings extends ListRecords
{
    protected static string $resource = ListingResource::class;

    public $sttCounter = 0;

    protected function getHeaderActions(): array
    {
        return [
            // Không cho tạo tin mới từ admin - đối tác tự đăng
            // Actions\CreateAction::make(),
        ];
    }
    
    public function mount(): void
    {
        parent::mount();
        $this->sttCounter = 0;
    }
}
