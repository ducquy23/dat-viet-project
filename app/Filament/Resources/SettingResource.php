<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';
    protected static ?string $navigationLabel = 'Cấu hình chung';
    protected static ?string $modelLabel = 'Cấu hình';
    protected static ?string $pluralModelLabel = 'Cấu hình';
    protected static ?string $navigationGroup = 'Cấu Hình Hệ Thống';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin chung')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->label('Tên site')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('site_slogan')
                            ->label('Slogan')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('logo_url')
                            ->label('Logo URL')
                            ->maxLength(255)
                            ->helperText('Đường dẫn đầy đủ tới logo (nếu để trống sẽ dùng mặc định)'),
                        Forms\Components\TextInput::make('og_image_url')
                            ->label('Ảnh OG mặc định')
                            ->maxLength(255)
                            ->helperText('Ảnh mặc định khi chia sẻ (Open Graph)'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Liên hệ hỗ trợ')
                    ->schema([
                        Forms\Components\TextInput::make('hotline')
                            ->label('Hotline')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('zalo')
                            ->label('Zalo')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('support_email')
                            ->label('Email hỗ trợ')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('support_message')
                            ->label('Thông điệp hỗ trợ')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Hiển thị VIP')
                    ->schema([
                        Forms\Components\TextInput::make('vip_limit')
                            ->label('Số tin VIP hiển thị')
                            ->numeric()
                            ->default(10),
                        Forms\Components\Select::make('vip_sort')
                            ->label('Cách sắp xếp VIP')
                            ->options([
                                'latest' => 'Mới nhất',
                                'most_view' => 'Nhiều view',
                            ])
                            ->default('latest'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('site_name')
                    ->label('Tên site')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hotline')
                    ->label('Hotline')
                    ->searchable(),
                Tables\Columns\TextColumn::make('support_email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vip_limit')
                    ->label('Tin VIP'),
                Tables\Columns\TextColumn::make('vip_sort')
                    ->label('Sắp xếp VIP'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}


