<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use App\Models\User;
use App\Models\Listing;
use App\Models\Package;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Thanh Toán';

    protected static ?string $modelLabel = 'Thanh Toán';

    protected static ?string $pluralModelLabel = 'Thanh Toán';

    protected static ?string $navigationGroup = 'Quản Lý Giao Dịch';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Đối tác')
                    ->options(fn () => User::query()
                        ->whereIn('role', ['user', 'moderator'])
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('listing_id')
                    ->label('Tin đăng')
                    ->relationship('listing', 'title')
                    ->searchable()
                    ->helperText('Tin đăng gắn với thanh toán (nếu có)'),
                Forms\Components\Select::make('package_id')
                    ->label('Gói')
                    ->relationship('package', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('transaction_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->label('Số tiền (VND)')
                    ->required()
                    ->numeric()
                    ->prefix('₫'),
                Forms\Components\TextInput::make('currency')
                    ->required()
                    ->maxLength(3)
                    ->default('VND'),
                Forms\Components\Select::make('payment_method')
                    ->label('Phương thức thanh toán')
                    ->options([
                        'bank_transfer' => 'Chuyển khoản',
                        'momo' => 'MoMo',
                        'vnpay' => 'VNPay',
                        'zalopay' => 'ZaloPay',
                        'cash' => 'Tiền mặt',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ xử lý',
                        'processing' => 'Đang xử lý',
                        'completed' => 'Hoàn thành',
                        'failed' => 'Thất bại',
                        'refunded' => 'Đã hoàn tiền',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\Textarea::make('payment_info')
                    ->label('Thông tin từ cổng thanh toán')
                    ->columnSpanFull()
                    ->rows(3)
                    ->disabled()
                    ->helperText('Dữ liệu raw trả về từ PayOS / cổng thanh toán (chỉ đọc)'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('paid_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Đối tác')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('listing.title')
                    ->label('Tin đăng')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('package.name')
                    ->label('Gói')
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Số tiền')
                    ->money('VND', true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('payment_method')
                    ->label('Phương thức')
                    ->colors([
                        'primary',
                        'success' => 'bank_transfer',
                        'warning' => 'momo',
                        'info' => 'vnpay',
                        'danger' => 'zalopay',
                        'secondary' => 'cash',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Trạng thái')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'secondary' => 'refunded',
                    ]),
                Tables\Columns\TextColumn::make('paid_at')
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
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ xử lý',
                        'processing' => 'Đang xử lý',
                        'completed' => 'Hoàn thành',
                        'failed' => 'Thất bại',
                        'refunded' => 'Đã hoàn tiền',
                    ]),
                SelectFilter::make('payment_method')
                    ->label('Phương thức')
                    ->options([
                        'bank_transfer' => 'Chuyển khoản',
                        'momo' => 'MoMo',
                        'vnpay' => 'VNPay',
                        'zalopay' => 'ZaloPay',
                        'cash' => 'Tiền mặt',
                    ]),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
