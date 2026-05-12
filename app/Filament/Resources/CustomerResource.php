<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section;
// use Filament\Infolists\Infolist;
// use Filament\Infolists\Components\TextEntry;


class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'الزبائن';
    protected static ?string $modelLabel = 'زبون';
    protected static ?string $pluralModelLabel = 'الزبائن';
    protected static ?string $activeNavigationIcon = 'heroicon-o-chevron-double-down';
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('المعلومات الشخصية')
                    ->description('قم بتعبئة الحقول التالية :')
                    ->schema(components: [
                        Forms\Components\TextInput::make('code')
                            ->label('الرمز')
                            ->placeholder('الرمز')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->placeholder('الاسم')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label( 'رقم الهاتف')
                            ->placeholder('09')
                            ->tel()
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('email')
                            ->label( 'الايميل')
                            ->placeholder('')
                            ->maxLength(255)
                            ->email(),
                        Forms\Components\Textarea::make('address')
                            ->label('العنوان')
                            ->placeholder('عنوان الزبون')
                            ->default(null)
                            ->columnSpanFull(),
                    ])->columns(4),

                Forms\Components\Section::make('المعلومات الأساسية:')
                    ->description('يجب تعبئة جميع هذه الحقول:')
                    ->schema(components: [
                        Forms\Components\TextInput::make('tax_number')
                            ->numeric()
                            ->required()
                            ->default(null),
                        Forms\Components\TextInput::make('commercial_register')
                            ->required()
                            ->default(null),
                        Forms\Components\TextInput::make('balance')
                            ->required()
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('current_balance')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('total_sales')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('total_orders')
                            ->required()
                            ->numeric()
                            ->default(null),
                        Forms\Components\TextInput::make('credit_limit')
                            ->required()
                            ->numeric()
                            ->default(0.00),

                        Forms\Components\TextInput::make('total_purchases')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                    ])->columns(3),
                Forms\Components\Section::make(' معلومات أخيرة:')
                    // ->description('')
                    ->schema([
                        Forms\Components\TextInput::make('type')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('contact_person')
                            ->maxLength(255)
                            ->default(null),

                        Forms\Components\TextInput::make('primary_warehouse_id')
                            ->numeric()
                            ->default(null),
                        Forms\Components\TextInput::make('secondary_warehouse_id')
                            ->numeric()
                            ->default(null),
                        // Forms\Components\DatePicker::make('start_date'),

                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull()
                            ->placeholder('الملاحظات .....'),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(4),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('الرمز')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(label: 'الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->label(label: 'رقم الهاتف'),
                Tables\Columns\TextColumn::make('email')
                    ->label(label: 'الايميل')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')
                    ->label('العنوان')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tax_number')
                    ->sortable(),
                Tables\Columns\TextColumn::make('commercial_register'),
                Tables\Columns\TextColumn::make('total_sales'),
                Tables\Columns\TextColumn::make('balance'),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_person')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('primary_warehouse_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('secondary_warehouse_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_orders')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('credit_limit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_balance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_purchases')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('notes')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(' تم الحذف بنجاح.')
                            ->icon('heroicon-o-x-circle')
                            ->color('danger')
                    )
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // public static function infolist(Infolist $infolist): Infolist
    // {
    //     return $infolist

    //         ->schema([
    //             TextEntry::make('title'),
    //             TextEntry::make('slug'),
    //             TextEntry::make('content'),
    //         ]);
    // }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationGroup(): ?string
    {
        return 'إدارة النظام';
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
