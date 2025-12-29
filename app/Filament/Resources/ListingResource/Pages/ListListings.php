<?php

namespace App\Filament\Resources\ListingResource\Pages;

use App\Filament\Resources\ListingResource;
use App\Models\Listing;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

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

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả')
                ->badge(fn () => Listing::withoutTrashed()->count())
                ->icon('heroicon-o-list-bullet'),
            'pending' => Tab::make('Chờ duyệt')
                ->badge(fn () => Listing::withoutTrashed()->where('status', 'pending')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->icon('heroicon-o-clock'),
            'approved' => Tab::make('Đã duyệt')
                ->badge(fn () => Listing::withoutTrashed()->where('status', 'approved')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved'))
                ->icon('heroicon-o-check-circle'),
            'rejected' => Tab::make('Từ chối')
                ->badge(fn () => Listing::withoutTrashed()->where('status', 'rejected')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected'))
                ->icon('heroicon-o-x-circle'),
            'expired' => Tab::make('Hết hạn')
                ->badge(fn () => Listing::withoutTrashed()->where('status', 'expired')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'expired'))
                ->icon('heroicon-o-exclamation-triangle'),
            'sold' => Tab::make('Đã bán')
                ->badge(fn () => Listing::withoutTrashed()->where('status', 'sold')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'sold'))
                ->icon('heroicon-o-check-badge'),
            'draft' => Tab::make('Nháp')
                ->badge(fn () => Listing::withoutTrashed()->where('status', 'draft')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft'))
                ->icon('heroicon-o-document'),
        ];
    }
}
