<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model; 

 
use App\Models\Warehouse;
use App\Enums\UserRole;     
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
       protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'المستخدمين';
    protected static ?string $modelLabel = 'مستخدم';
    protected static ?string $pluralModelLabel = 'المستخدمين';
    protected static ?string $activeNavigationIcon = 'heroicon-o-chevron-double-down';

    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('المعلومات الأساسية')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('username')
                                    ->label('اسم المستخدم')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50),
                                    
                                Forms\Components\TextInput::make('name')
                                    ->label('الاسم الكامل')
                                    ->required()
                                    ->maxLength(100),
                            ])->columns(2),
                            
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->label('البريد الإلكتروني')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                    
                                Forms\Components\TextInput::make('phone')
                                    ->label('الهاتف')
                                    ->tel()
                                    ->maxLength(20),
                            ])->columns(2),
                    ]),
                    
                Forms\Components\Section::make('الأمان والدور')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('password')
                                    ->label('كلمة المرور')
                                    ->password()
                                    ->required(fn ($operation) => $operation === 'create')
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->revealable(),
                                    
                                Forms\Components\Select::make('role')
                                    ->label('الدور')
                                    ->options([
                                        'admin' => 'مدير النظام',
                                        'manager' => 'مدير',
                                        'warehouse_keeper' => 'أمين مخزن',
                                        'accountant' => 'محاسب',
                                        'viewer' => 'مشاهد',
                                    ])
                                    ->required()
                                    ->native(false),
                            ])->columns(2),
                            
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('الحساب مفعل')
                                    ->default(true)
                                    ->inline(false),
                                    
                                Forms\Components\DatePicker::make('email_verified_at')
                                    ->label('تاريخ تأكيد البريد')
                                    ->displayFormat('d/m/Y'),
                            ])->columns(2),
                    ]),
                    
                Forms\Components\Section::make('المخازن المسموحة')
                    ->schema([
                        Forms\Components\Select::make('primary_warehouse_id')
                            ->label('المخزن الأساسي')
                            ->relationship('primaryWarehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('المخزن الرئيسي الذي يمكن للمستخدم الوصول إليه'),
                            
                        Forms\Components\Select::make('secondary_warehouse_id')
                            ->label('المخزن الثانوي')
                            ->relationship('secondaryWarehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('مخزن إضافي يمكن للمستخدم الوصول إليه'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('الإعدادات الإضافية')
                    ->schema([
                        Forms\Components\KeyValue::make('settings')
                            ->label('إعدادات إضافية')
                            ->keyLabel('المفتاح')
                            ->valueLabel('القيمة')
                            ->addable(true)
                            ->deletable(true)
                            ->editableKeys(true)
                            ->editableValues(true),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('username')
                    ->label('اسم المستخدم')
                    ->sortable()
                    ->searchable()
                    ->color('gray'),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                    
                Tables\Columns\TextColumn::make('role')
                    ->label('الدور')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'admin' => 'مدير النظام',
                            'manager' => 'مدير',
                            'warehouse_keeper' => 'أمين مخزن',
                            'accountant' => 'محاسب',
                            'viewer' => 'مشاهد',
                            default => $state,
                        };
                    })
                    ->color(fn ($state) => $state?->color())
                    // ->color(function ($state): string {
                    //     return match($state) {
                    //         'admin' => 'danger',
                    //         'manager' => 'success',
                    //         'warehouse_keeper' => 'info',
                    //         'accountant' => 'warning',
                    //         'viewer' => 'gray',
                    //         default => 'gray',
                    //     };
                    // })
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('مفعل')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('primaryWarehouse.name')
                    ->label('المخزن الأساسي')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->primaryWarehouse?->name)
                    ->icon('heroicon-o-building-office')
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('الدور')
                    ->options([
                        'admin' => 'مدير النظام',
                        'manager' => 'مدير',
                        'warehouse_keeper' => 'أمين مخزن',
                        'accountant' => 'محاسب',
                        'viewer' => 'مشاهد',
                    ])
                    ->multiple(),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحساب مفعل')
                    ->placeholder('جميع الحسابات')
                    ->trueLabel('المفعلة فقط')
                    ->falseLabel('المعطلة فقط'),
                    
                Tables\Filters\SelectFilter::make('primary_warehouse_id')
                    ->label('المخزن الأساسي')
                    ->relationship('primaryWarehouse', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary'),
                    
                Tables\Actions\Action::make('toggleActive')
                    ->label(fn ($record) => $record->is_active ? 'تعطيل' : 'تفعيل')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                    ->action(function (User $record) {
                        $record->update(['is_active' => !$record->is_active]);
                    })
                    ->requiresConfirmation(),
                    
                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn ($record) => $record->id !== 1),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('تفعيل المحدد')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);
                        })
                        ->requiresConfirmation(),
                        
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('تعطيل المحدد')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => false]);
                        })
                        ->requiresConfirmation(),
                        
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد')
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'إدارة النظام';
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

}
