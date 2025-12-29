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
use Filament\Infolists;
use Filament\Infolists\Infolist;
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
                Forms\Components\Tabs::make('ListingTabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Thông tin cơ bản')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
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
                            ]),

                        Forms\Components\Tabs\Tab::make('Hình ảnh')
                            ->icon('heroicon-o-photo')
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
                            ]),

                        Forms\Components\Tabs\Tab::make('Vị trí & Bản đồ')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Forms\Components\Section::make('Vị trí trên bản đồ')
                                    ->schema([
                                        Forms\Components\View::make('filament.forms.components.leaflet-map')
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('latitude')
                                            ->label('Vĩ độ')
                                            ->required()
                                            ->numeric()
                                            ->step(0.00000001)
                                            ->helperText('Hoặc nhập trực tiếp, hoặc click/kéo marker trên bản đồ'),
                                        Forms\Components\TextInput::make('longitude')
                                            ->label('Kinh độ')
                                            ->required()
                                            ->numeric()
                                            ->step(0.00000001)
                                            ->helperText('Hoặc nhập trực tiếp, hoặc click/kéo marker trên bản đồ'),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Giá & Diện tích')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\Section::make('Thông tin giá và diện tích')
                                    ->schema([
                                        Forms\Components\TextInput::make('price_per_m2')
                                            ->label('Đơn giá /m² (triệu đồng/m²)')
                                            ->required()
                                            ->numeric()
                                            ->prefix('₫')
                                            ->step(0.01)
                                            ->helperText('Hệ thống sẽ tự tính Giá tổng từ Đơn giá × Diện tích')
                                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                                                if ($state !== null) {
                                                    $formatted = rtrim(rtrim(number_format((float) $state, 2, '.', ''), '0'), '.');
                                                    $component->state($formatted);
                                                }
                                            })
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                                // Tự động tính giá tổng khi đơn giá/m² hoặc diện tích thay đổi
                                                $area = $get('area');
                                                if ($state && $area && $area > 0) {
                                                    $totalPrice = $state * $area;
                                                    $set('price', $totalPrice);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('price')
                                            ->label('Giá tổng (triệu đồng)')
                                            ->numeric()
                                            ->prefix('₫')
                                            ->step(0.01)
                                            ->disabled()
                                            ->dehydrated()
                                            ->helperText('Tự động tính từ Đơn giá × Diện tích')
                                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                                                if ($state !== null) {
                                                    $formatted = rtrim(rtrim(number_format((float) $state, 2, '.', ''), '0'), '.');
                                                    $component->state($formatted);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('area')
                                            ->label('Diện tích (m²)')
                                            ->required()
                                            ->numeric()
                                            ->suffix('m²')
                                            ->step(0.01)
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                                // Tự động tính giá tổng khi diện tích thay đổi
                                                $pricePerM2 = $get('price_per_m2');
                                                if ($pricePerM2 && $state && $state > 0) {
                                                    $totalPrice = $pricePerM2 * $state;
                                                    $set('price', $totalPrice);
                                                }
                                            })
                                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                                                if ($state !== null) {
                                                    $formatted = rtrim(rtrim(number_format((float) $state, 2, '.', ''), '0'), '.');
                                                    $component->state($formatted);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('front_width')
                                            ->label('Mặt tiền (m)')
                                            ->numeric()
                                            ->suffix('m')
                                            ->step(0.01)
                                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                                                if ($state !== null) {
                                                    $formatted = rtrim(rtrim(number_format((float) $state, 2, '.', ''), '0'), '.');
                                                    $component->state($formatted);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('depth')
                                            ->label('Chiều sâu (m)')
                                            ->numeric()
                                            ->suffix('m')
                                            ->step(0.01)
                                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                                                if ($state !== null) {
                                                    $formatted = rtrim(rtrim(number_format((float) $state, 2, '.', ''), '0'), '.');
                                                    $component->state($formatted);
                                                }
                                            }),
                                    ])->columns(3),
                            ]),

                        Forms\Components\Tabs\Tab::make('Pháp lý & Đường')
                            ->icon('heroicon-o-document-text')
                            ->schema([
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
                                            ->step(0.01)
                                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                                                if ($state !== null) {
                                                    $formatted = rtrim(rtrim(number_format((float) $state, 2, '.', ''), '0'), '.');
                                                    $component->state($formatted);
                                                }
                                            }),
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
                            ]),

                        Forms\Components\Tabs\Tab::make('Liên hệ')
                            ->icon('heroicon-o-phone')
                            ->schema([
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
                            ]),

                        Forms\Components\Tabs\Tab::make('Trạng thái & SEO')
                            ->icon('heroicon-o-flag')
                            ->schema([
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
                                        Forms\Components\Textarea::make('meta_description')
                                            ->label('Mô tả SEO')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ])->collapsible(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Thống kê')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
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
                            ]),
                    ])
                    ->activeTab(1)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['primaryImage', 'images']))
            ->columns([
                Tables\Columns\TextColumn::make('stt')
                    ->label('STT')
                    ->getStateUsing(function ($record, $livewire) {
                        // Lấy records hiện tại
                        $records = $livewire->getTableRecords();
                        
                        if (!$records || $records->isEmpty()) {
                            return '';
                        }
                        
                        // Tìm index của record trong collection
                        $index = $records->values()->search(function ($item) use ($record) {
                            return $item->id === $record->id;
                        });
                        
                        if ($index === false) {
                            return '';
                        }
                        
                        // Tính STT
                        $page = max(1, (int) request()->get('page', 1));
                        $perPage = (int) ($livewire->tableRecordsPerPage ?? 10);
                        
                        return ($page - 1) * $perPage + $index + 1;
                    })
                    ->sortable(false),
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Ảnh đại diện')
                    ->disk('public')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(asset('images/Image-not-found.png'))
                    ->getStateUsing(function ($record) {
                        // Thử lấy từ field thumbnail trước
                        if ($record->thumbnail) {
                            return $record->thumbnail;
                        }
                        // Nếu không có, lấy từ primaryImage
                        if ($record->primaryImage) {
                            return $record->primaryImage->thumbnail_path ?? $record->primaryImage->image_path;
                        }
                        // Nếu không có primaryImage, lấy ảnh đầu tiên trong gallery
                        $firstImage = $record->images()->first();
                        if ($firstImage) {
                            return $firstImage->thumbnail_path ?? $firstImage->image_path;
                        }
                        return null;
                    }),
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
                    ->formatStateUsing(fn ($state) => formatPrice($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('area')
                    ->label('Diện tích')
                    ->formatStateUsing(function ($state) {
                        if ($state === null) return 'Chưa cập nhật';
                        return formatNumber($state) . ' m²';
                    })
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('Tiêu đề')
                            ->columnSpanFull()
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Mô tả')
                            ->columnSpanFull()
                            ->html(),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Đối tác'),
                        Infolists\Components\TextEntry::make('category.name')
                            ->label('Danh mục'),
                        Infolists\Components\TextEntry::make('city.name')
                            ->label('Tỉnh/Thành phố'),
                        Infolists\Components\TextEntry::make('district.name')
                            ->label('Quận/Huyện'),
                        Infolists\Components\TextEntry::make('package.name')
                            ->label('Gói đăng tin'),
                        Infolists\Components\TextEntry::make('status')
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
                    ])
                    ->columns(2),
                
                Infolists\Components\Section::make('Thông tin giá và diện tích')
                    ->schema([
                        Infolists\Components\TextEntry::make('price')
                            ->label('Giá')
                            ->formatStateUsing(fn ($state) => formatPrice($state)),
                        Infolists\Components\TextEntry::make('price_per_m2')
                            ->label('Đơn giá /m²')
                            ->formatStateUsing(function ($state, $record) {
                                $formatted = formatPricePerM2($state, $record->price, $record->area);
                                if ($formatted) {
                                    return str_replace(' tr/m²', ' triệu/m²', $formatted);
                                }
                                return 'Chưa cập nhật';
                            }),
                        Infolists\Components\TextEntry::make('area')
                            ->label('Diện tích')
                            ->formatStateUsing(function ($state) {
                                if ($state === null) return 'Chưa cập nhật';
                                return formatNumber($state) . ' m²';
                            }),
                        Infolists\Components\TextEntry::make('front_width')
                            ->label('Mặt tiền')
                            ->formatStateUsing(fn ($state) => $state ? formatNumber($state) . ' m' : 'Chưa cập nhật'),
                        Infolists\Components\TextEntry::make('depth')
                            ->label('Chiều sâu')
                            ->formatStateUsing(fn ($state) => $state ? formatNumber($state) . ' m' : 'Chưa cập nhật'),
                    ])
                    ->columns(3),
                
                Infolists\Components\Section::make('Thông tin pháp lý và đường')
                    ->schema([
                        Infolists\Components\TextEntry::make('legal_status')
                            ->label('Tình trạng pháp lý')
                            ->formatStateUsing(fn ($state) => $state ?: 'Chưa cập nhật'),
                        Infolists\Components\TextEntry::make('road_type')
                            ->label('Loại đường')
                            ->formatStateUsing(fn ($state) => $state ?: 'Chưa cập nhật'),
                        Infolists\Components\TextEntry::make('road_width')
                            ->label('Độ rộng đường')
                            ->formatStateUsing(fn ($state) => $state ? formatNumber($state) . ' m' : 'Chưa cập nhật'),
                        Infolists\Components\TextEntry::make('direction')
                            ->label('Hướng')
                            ->formatStateUsing(fn ($state) => $state ?: 'Chưa cập nhật'),
                        Infolists\Components\IconEntry::make('has_road_access')
                            ->label('Có đường ô tô vào')
                            ->boolean(),
                    ])
                    ->columns(3),
                
                Infolists\Components\Section::make('Thông tin liên hệ')
                    ->schema([
                        Infolists\Components\TextEntry::make('contact_name')
                            ->label('Tên người liên hệ'),
                        Infolists\Components\TextEntry::make('contact_phone')
                            ->label('Số điện thoại')
                            ->copyable()
                            ->copyMessage('Đã sao chép số điện thoại'),
                        Infolists\Components\TextEntry::make('contact_zalo')
                            ->label('Zalo')
                            ->formatStateUsing(fn ($state) => $state ?: 'Chưa cập nhật'),
                    ])
                    ->columns(3),
                
                Infolists\Components\Section::make('Hình ảnh')
                    ->schema([
                        Infolists\Components\ImageEntry::make('thumbnail')
                            ->label('Ảnh đại diện')
                            ->disk('public')
                            ->getStateUsing(function ($record) {
                                if ($record->thumbnail) {
                                    return $record->thumbnail;
                                }
                                if ($record->primaryImage) {
                                    return $record->primaryImage->thumbnail_path ?? $record->primaryImage->image_path;
                                }
                                $firstImage = $record->images()->first();
                                return $firstImage ? ($firstImage->thumbnail_path ?? $firstImage->image_path) : null;
                            })
                            ->columnSpanFull(),
                        Infolists\Components\ViewEntry::make('gallery')
                            ->label('Gallery ảnh')
                            ->view('filament.infolists.components.image-gallery')
                            ->columnSpanFull()
                            ->getStateUsing(function ($record) {
                                // Load images nếu chưa được load
                                $images = $record->relationLoaded('images') 
                                    ? $record->images 
                                    : $record->images()->get();
                                
                                return $images->map(function ($image) {
                                    return [
                                        'url' => $image->thumbnail_path ?? $image->image_path,
                                        'full_url' => $image->image_path,
                                    ];
                                })->toArray();
                            }),
                    ])
                    ->collapsible(),
                
                Infolists\Components\Section::make('Vị trí trên bản đồ')
                    ->schema([
                        Infolists\Components\TextEntry::make('address')
                            ->label('Địa chỉ')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('latitude')
                            ->label('Vĩ độ'),
                        Infolists\Components\TextEntry::make('longitude')
                            ->label('Kinh độ'),
                        Infolists\Components\ViewEntry::make('map')
                            ->label('Bản đồ')
                            ->view('filament.infolists.components.google-map')
                            ->columnSpanFull()
                            ->getStateUsing(function ($record) {
                                return [
                                    'latitude' => $record->latitude,
                                    'longitude' => $record->longitude,
                                    'address' => $record->address,
                                    'title' => $record->title,
                                ];
                            }),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                Infolists\Components\Section::make('Thông tin khác')
                    ->schema([
                        Infolists\Components\TextEntry::make('planning_info')
                            ->label('Thông tin quy hoạch')
                            ->columnSpanFull()
                            ->formatStateUsing(fn ($state) => $state ?: 'Chưa cập nhật'),
                        Infolists\Components\IconEntry::make('deposit_online')
                            ->label('Có đặt cọc online')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('meta_description')
                            ->label('Mô tả SEO')
                            ->columnSpanFull()
                            ->formatStateUsing(fn ($state) => $state ?: 'Chưa cập nhật'),
                        Infolists\Components\TextEntry::make('slug')
                            ->label('Slug (URL)')
                            ->columnSpanFull()
                            ->copyable(),
                    ])
                    ->collapsible(),
                
                Infolists\Components\Section::make('Thống kê')
                    ->schema([
                        Infolists\Components\TextEntry::make('views_count')
                            ->label('Lượt xem')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0)),
                        Infolists\Components\TextEntry::make('favorites_count')
                            ->label('Lượt yêu thích')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0)),
                        Infolists\Components\TextEntry::make('contacts_count')
                            ->label('Lượt liên hệ')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0)),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Ngày đăng')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('approved_at')
                            ->label('Ngày duyệt')
                            ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : '-'),
                        Infolists\Components\TextEntry::make('expires_at')
                            ->label('Ngày hết hạn')
                            ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : '-'),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
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
