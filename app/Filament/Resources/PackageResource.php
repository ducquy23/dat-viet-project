<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Gói Đăng Tin';

    protected static ?string $modelLabel = 'Gói Đăng Tin';

    protected static ?string $pluralModelLabel = 'Gói Đăng Tin';

    protected static ?string $navigationGroup = 'Cấu Hình Hệ Thống';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên gói')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText('Tên hiển thị của gói đăng tin (ví dụ: Gói Cơ Bản, Gói VIP)'),
                        Forms\Components\TextInput::make('code')
                            ->label('Mã gói')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Mã định danh duy nhất cho gói (ví dụ: BASIC, VIP, PREMIUM)')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Mô tả chi tiết về gói đăng tin'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Giá và thời hạn')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Giá (VND)')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->prefix('₫')
                            ->helperText('Giá của gói tính bằng VND'),
                        Forms\Components\TextInput::make('duration_days')
                            ->label('Thời hạn (ngày)')
                            ->required()
                            ->numeric()
                            ->default(30)
                            ->suffix('ngày')
                            ->helperText('Số ngày tin đăng được hiển thị'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Tính năng và ưu tiên')
                    ->schema([
                        Forms\Components\TextInput::make('priority')
                            ->label('Độ ưu tiên')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Số càng cao, tin đăng càng được ưu tiên hiển thị'),
                        Forms\Components\Textarea::make('features')
                            ->label('Tính năng')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Danh sách các tính năng của gói (mỗi tính năng một dòng)'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Trạng thái')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Kích hoạt')
                            ->default(true)
                            ->required()
                            ->helperText('Bật/tắt hiển thị gói trên website'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_days')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}
