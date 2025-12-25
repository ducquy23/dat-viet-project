<?php

namespace App\Filament\Resources\ListingResource\Pages;

use App\Filament\Resources\ListingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewListing extends ViewRecord
{
    protected static string $resource = ListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Eager load images để hiển thị trong infolist
        $this->record->load(['images', 'primaryImage']);
        return $data;
    }
    
    protected function getFooterWidgets(): array
    {
        return [];
    }
    
    public function getFooterWidgetsData(): array
    {
        return [];
    }
}
