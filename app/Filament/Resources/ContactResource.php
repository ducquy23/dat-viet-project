<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use App\Models\User;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Liên Hệ';

    protected static ?string $modelLabel = 'Liên Hệ';

    protected static ?string $pluralModelLabel = 'Liên Hệ';

    protected static ?string $navigationGroup = 'Quản Lý Giao Dịch';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('listing_id')
                    ->label('Tin đăng')
                    ->relationship('listing', 'title')
                    ->searchable()
                    ->required()
                    ->helperText('Tin đăng được liên hệ'),
                Forms\Components\Select::make('user_id')
                    ->label('Đối tác')
                    ->options(fn () => User::query()
                        ->whereIn('role', ['user', 'moderator'])
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->helperText('Chọn đối tác (nếu người liên hệ là đối tác)'),
                Forms\Components\TextInput::make('visitor_name')
                    ->label('Tên khách')
                    ->maxLength(255),
                Forms\Components\TextInput::make('visitor_phone')
                    ->label('Số điện thoại')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('visitor_email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\Select::make('contact_type')
                    ->label('Loại liên hệ')
                    ->options([
                        'call' => 'Gọi điện',
                        'zalo' => 'Zalo',
                        'message' => 'Tin nhắn',
                        'deposit' => 'Đặt cọc',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('message')
                    ->label('Tin nhắn')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ xử lý',
                        'contacted' => 'Đã liên hệ',
                        'closed' => 'Đã đóng',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('listing.title')
                    ->label('Tin đăng')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Đối tác')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('visitor_name')
                    ->label('Khách')
                    ->searchable(),
                Tables\Columns\TextColumn::make('visitor_phone')
                    ->label('SĐT')
                    ->searchable(),
                Tables\Columns\TextColumn::make('visitor_email')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('contact_type')
                    ->label('Loại')
                    ->colors([
                        'primary',
                        'success' => 'call',
                        'warning' => 'zalo',
                        'info' => 'message',
                        'danger' => 'deposit',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Trạng thái')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'contacted',
                        'secondary' => 'closed',
                    ]),
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
                SelectFilter::make('contact_type')
                    ->label('Loại liên hệ')
                    ->options([
                        'call' => 'Gọi điện',
                        'zalo' => 'Zalo',
                        'message' => 'Tin nhắn',
                        'deposit' => 'Đặt cọc',
                    ]),
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ xử lý',
                        'contacted' => 'Đã liên hệ',
                        'closed' => 'Đã đóng',
                    ]),
                SelectFilter::make('user_id')
                    ->label('Đối tác')
                    ->options(fn () => User::query()
                        ->whereIn('role', ['user', 'moderator'])
                        ->orderBy('name')
                        ->pluck('name', 'id')),
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
