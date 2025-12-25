<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    public function mount(): void
    {
        parent::mount();
        
        // Tự động redirect đến edit record đầu tiên hoặc create nếu chưa có
        $setting = Setting::first();
        
        if ($setting) {
            $this->redirect(SettingResource::getUrl('edit', ['record' => $setting]));
        } else {
            $this->redirect(SettingResource::getUrl('create'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}


