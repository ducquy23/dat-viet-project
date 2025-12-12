<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdResource\Pages;
use App\Filament\Resources\AdResource\RelationManagers;
use App\Models\Ad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdResource extends Resource
{
    protected static ?string $model = Ad::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Quảng Cáo';

    protected static ?string $modelLabel = 'Quảng Cáo';

    protected static ?string $pluralModelLabel = 'Quảng Cáo';

    protected static ?string $navigationGroup = 'Quản Lý Nội Dung';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Tiêu đề')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Hình ảnh quảng cáo')
                    ->schema([
                        Forms\Components\FileUpload::make('image_url')
                            ->label('Hình ảnh')
                            ->image()
                            ->directory('ads')
                            ->disk('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(5120) // 5MB
                            ->helperText('Kích thước tối đa: 5MB. Tỷ lệ khuyến nghị: 16:9 cho banner, 1:1 cho sidebar')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Vị trí hiển thị')
                    ->schema([
                        Forms\Components\Select::make('position')
                            ->label('Vị trí')
                            ->options([
                                'top' => 'Top Banner',
                                'sidebar_left' => 'Sidebar Trái',
                                'sidebar_right' => 'Sidebar Phải',
                                'bottom' => 'Bottom Banner',
                            ])
                            ->required()
                            ->native(false)
                            ->helperText('Chọn vị trí hiển thị quảng cáo trên website'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Thứ tự sắp xếp')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Số nhỏ hơn sẽ hiển thị trước'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Liên kết và CTA')
                    ->schema([
                        Forms\Components\TextInput::make('link_url')
                            ->label('URL liên kết')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://example.com hoặc #')
                            ->helperText('URL khi người dùng click vào quảng cáo'),
                        Forms\Components\TextInput::make('link_text')
                            ->label('Text nút CTA')
                            ->maxLength(255)
                            ->placeholder('Ví dụ: Khám phá ngay, Tải về')
                            ->helperText('Text hiển thị trên nút Call-to-Action'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Trạng thái và thời gian')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Kích hoạt')
                            ->required()
                            ->default(true)
                            ->helperText('Bật/tắt hiển thị quảng cáo'),
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Ngày bắt đầu')
                            ->helperText('Quảng cáo sẽ hiển thị từ ngày này (để trống = ngay lập tức)'),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Ngày kết thúc')
                            ->helperText('Quảng cáo sẽ ẩn sau ngày này (để trống = không giới hạn)'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Hình ảnh')
                    ->disk('public')
                    ->size(100),
                Tables\Columns\TextColumn::make('link_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('link_text')
                    ->searchable(),
                Tables\Columns\TextColumn::make('position'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
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
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAd::route('/create'),
            'edit' => Pages\EditAd::route('/{record}/edit'),
        ];
    }
}
