<?php

namespace App\Filament\Resources\ListingResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image_path')
                    ->label('Hình ảnh')
                    ->image()
                    ->directory('listings')
                    ->disk('public')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->maxSize(5120) // 5MB
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('thumbnail_path')
                    ->label('Ảnh thumbnail (tự động tạo nếu để trống)')
                    ->image()
                    ->directory('listings/thumbnails')
                    ->disk('public')
                    ->maxSize(1024) // 1MB
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Thứ tự sắp xếp')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                Forms\Components\Toggle::make('is_primary')
                    ->label('Ảnh chính')
                    ->default(false)
                    ->helperText('Chỉ một ảnh có thể là ảnh chính'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('image_path')
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Hình ảnh')
                    ->disk('public')
                    ->size(100),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_primary')
                    ->label('Ảnh chính')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_primary')
                    ->label('Ảnh chính'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('set_primary')
                    ->label('Đặt làm ảnh chính')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->action(function ($record) {
                        // Bỏ ảnh chính của tất cả ảnh khác
                        $record->listing->images()->update(['is_primary' => false]);
                        // Đặt ảnh này làm ảnh chính
                        $record->update(['is_primary' => true]);
                    })
                    ->visible(fn ($record) => !$record->is_primary),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
