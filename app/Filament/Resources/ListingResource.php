<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListingResource\Pages;
use App\Filament\Resources\ListingResource\RelationManagers;
use App\Models\Listing;
use App\Models\User;
use App\Models\Category;
use App\Models\City;
use App\Models\District;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class ListingResource extends Resource
{
    protected static ?string $model = Listing::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Tin Đăng';

    protected static ?string $modelLabel = 'Tin Đăng';

    protected static ?string $pluralModelLabel = 'Tin Đăng';

    protected static ?string $navigationGroup = 'Quản Lý Nội Dung';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hình ảnh')
                    ->description('Thumbnail là ảnh đại diện chính, Gallery là các ảnh bổ sung')
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Ảnh đại diện (Thumbnail)')
                            ->image()
                            ->directory('listings/thumbnails')
                            ->disk('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(5120) // 5MB
                            ->helperText('Ảnh này sẽ hiển thị làm ảnh đại diện cho tin đăng')
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('gallery_images')
                            ->label('Gallery ảnh')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Hình ảnh')
                                    ->image()
                                    ->directory('listings/gallery')
                                    ->disk('public')
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        null,
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->maxSize(5120) // 5MB
                                    ->required(),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Thứ tự')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Thêm ảnh')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                $image = $state['image'] ?? null;
                                
                                // Nếu là array, kiểm tra có phần tử và lấy phần tử đầu tiên
                                if (is_array($image)) {
                                    if (!empty($image) && isset($image[0])) {
                                        $image = $image[0];
                                    } else {
                                        $image = null;
                                    }
                                }
                                
                                // Nếu là string và có giá trị, lấy tên file
                                if (is_string($image) && !empty($image)) {
                                    return basename($image);
                                }
                                
                                return 'Ảnh mới';
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Đối tác')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                        Forms\Components\Select::make('category_id')
                            ->label('Danh mục')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('city_id')
                            ->label('Tỉnh/Thành phố')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('district_id', null)),
                        Forms\Components\Select::make('district_id')
                            ->label('Quận/Huyện')
                            ->relationship('district', 'name', fn (Builder $query, Forms\Get $get) => 
                                $query->where('city_id', $get('city_id')))
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('package_id')
                            ->label('Gói đăng tin')
                            ->relationship('package', 'name')
                            ->required()
                            ->default(1),
                    ])->columns(2),

                Forms\Components\Section::make('Nội dung tin đăng')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Tiêu đề')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('address')
                            ->label('Địa chỉ')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Vị trí trên bản đồ')
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('Vĩ độ')
                            ->required()
                            ->numeric()
                            ->step(0.00000001),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Kinh độ')
                            ->required()
                            ->numeric()
                            ->step(0.00000001),
                    ])->columns(2),

                Forms\Components\Section::make('Thông tin giá và diện tích')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Giá (triệu đồng)')
                            ->required()
                            ->numeric()
                            ->prefix('₫')
                            ->step(0.01),
                        Forms\Components\TextInput::make('price_per_m2')
                            ->label('Đơn giá /m²')
                            ->numeric()
                            ->prefix('₫')
                            ->step(0.01),
                        Forms\Components\TextInput::make('area')
                            ->label('Diện tích (m²)')
                            ->required()
                            ->numeric()
                            ->suffix('m²')
                            ->step(0.01),
                        Forms\Components\TextInput::make('front_width')
                            ->label('Mặt tiền (m)')
                            ->numeric()
                            ->suffix('m')
                            ->step(0.01),
                        Forms\Components\TextInput::make('depth')
                            ->label('Chiều sâu (m)')
                            ->numeric()
                            ->suffix('m')
                            ->step(0.01),
                    ])->columns(3),

                Forms\Components\Section::make('Thông tin pháp lý và đường')
                    ->schema([
                        Forms\Components\Select::make('legal_status')
                            ->label('Tình trạng pháp lý')
                            ->options([
                                'Sổ đỏ' => 'Sổ đỏ',
                                'Sổ hồng' => 'Sổ hồng',
                                'Đang làm sổ' => 'Đang làm sổ',
                                'Giấy tờ khác' => 'Giấy tờ khác',
                            ]),
                        Forms\Components\Select::make('road_type')
                            ->label('Loại đường')
                            ->options([
                                'Ô tô' => 'Ô tô',
                                'Hẻm' => 'Hẻm',
                                'Đường đất' => 'Đường đất',
                            ]),
                        Forms\Components\TextInput::make('road_width')
                            ->label('Độ rộng đường (m)')
                            ->numeric()
                            ->suffix('m')
                            ->step(0.01),
                        Forms\Components\Select::make('direction')
                            ->label('Hướng')
                            ->options([
                                'Đông' => 'Đông',
                                'Tây' => 'Tây',
                                'Nam' => 'Nam',
                                'Bắc' => 'Bắc',
                                'Đông Nam' => 'Đông Nam',
                                'Đông Bắc' => 'Đông Bắc',
                                'Tây Nam' => 'Tây Nam',
                                'Tây Bắc' => 'Tây Bắc',
                            ]),
                        Forms\Components\Toggle::make('has_road_access')
                            ->label('Có đường ô tô vào')
                            ->default(false),
                    ])->columns(3),

                Forms\Components\Section::make('Thông tin liên hệ')
                    ->schema([
                        Forms\Components\TextInput::make('contact_name')
                            ->label('Tên người liên hệ')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('contact_zalo')
                            ->label('Zalo')
                            ->maxLength(255),
                    ])->columns(3),

                Forms\Components\Section::make('Trạng thái và duyệt tin')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'draft' => 'Nháp',
                                'pending' => 'Chờ duyệt',
                                'approved' => 'Đã duyệt',
                                'rejected' => 'Từ chối',
                                'expired' => 'Hết hạn',
                                'sold' => 'Đã bán',
                            ])
                            ->required()
                            ->default('pending')
                            ->helperText('Khi đổi trạng thái thành "Đã duyệt", ngày duyệt sẽ tự động được cập nhật'),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Lý do từ chối')
                            ->rows(3)
                            ->visible(fn (Forms\Get $get) => $get('status') === 'rejected')
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Ngày hết hạn')
                            ->nullable()
                            ->helperText('Ngày tin đăng hết hạn (để trống nếu không giới hạn)'),
                    ])->columns(2),

                Forms\Components\Section::make('Thông tin khác')
                    ->schema([
                        Forms\Components\Textarea::make('planning_info')
                            ->label('Thông tin quy hoạch')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('deposit_online')
                            ->label('Có đặt cọc online')
                            ->default(false),
                        Forms\Components\Textarea::make('meta_description')
                            ->label('Mô tả SEO')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->collapsible(),

                Forms\Components\Section::make('Thống kê')
                    ->schema([
                        Forms\Components\Placeholder::make('views_count')
                            ->label('Lượt xem')
                            ->content(fn ($record) => number_format($record?->views_count ?? 0)),
                        Forms\Components\Placeholder::make('favorites_count')
                            ->label('Lượt yêu thích')
                            ->content(fn ($record) => number_format($record?->favorites_count ?? 0)),
                        Forms\Components\Placeholder::make('contacts_count')
                            ->label('Lượt liên hệ')
                            ->content(fn ($record) => number_format($record?->contacts_count ?? 0)),
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Ngày đăng')
                            ->content(fn ($record) => $record?->created_at?->format('d/m/Y H:i') ?? '-'),
                    ])->columns(4)
                    ->visible(fn ($record) => $record !== null)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Đối tác')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->limit(50)
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Tỉnh/TP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('district.name')
                    ->label('Quận/Huyện')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Giá')
                    ->money('VND')
                    ->sortable(),
                Tables\Columns\TextColumn::make('area')
                    ->label('Diện tích')
                    ->suffix(' m²')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        'expired' => 'gray',
                        'sold' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Nháp',
                        'pending' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                        'expired' => 'Hết hạn',
                        'sold' => 'Đã bán',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Lượt xem')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đăng')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'draft' => 'Nháp',
                        'pending' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                        'expired' => 'Hết hạn',
                        'sold' => 'Đã bán',
                    ]),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Đối tác')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Danh mục')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('city_id')
                    ->label('Tỉnh/Thành phố')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Duyệt')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Listing $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                        ]);
                    })
                    ->visible(fn (Listing $record) => $record->status === 'pending'),
                Tables\Actions\Action::make('reject')
                    ->label('Từ chối')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Lý do từ chối')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Listing $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                    })
                    ->visible(fn (Listing $record) => in_array($record->status, ['pending', 'approved'])),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Duyệt đã chọn')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'status' => 'approved',
                                    'approved_at' => now(),
                                ]);
                            });
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // Đã chuyển sang section "Hình ảnh" trong form chính
            // RelationManagers\ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListListings::route('/'),
            'create' => Pages\CreateListing::route('/create'),
            'view' => Pages\ViewListing::route('/{record}'),
            'edit' => Pages\EditListing::route('/{record}/edit'),
        ];
    }
}
