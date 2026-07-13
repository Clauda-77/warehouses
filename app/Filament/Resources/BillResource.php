<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\Pages;
use App\Filament\Resources\BillResource\RelationManagers\BillRecordsRelationManager;
use App\Models\Bill;
use App\Enums\BillType;
use App\Enums\BillStatus;
use Filament\Tables\Grouping\Group;
use Filament\Navigation\NavigationItem;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'المذكرات';
    protected static ?string $modelLabel = 'مذكرة';
    protected static ?string $pluralModelLabel = 'المذكرات';
    protected static ?string $activeNavigationIcon = 'heroicon-o-chevron-double-down';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->select(['id', 'bill_number', 'date', 'party_name', 'total', 'status']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->when(request('type'), fn($q, $type) => $q->where('type', $type)))
            ->columns([
                TextColumn::make('bill_number')
                    ->label('رقم المذكرة')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label('التاريخ')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('party_name')
                    ->label('الطرف')
                    ->searchable()
                    ->limit(25),

                TextColumn::make('total')
                    ->label('الإجمالي')
                    ->money('SDG')
                    ->sortable(),

                TextColumn::make('billRecords_count')
                    ->label('عدد الأصناف')
                    ->counts('billRecords')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('advanced_search')
                    ->label('البحث المتقدم')
                    ->form([
                        ToggleButtons::make('search_mode')
                            ->label('نوع البحث')
                            ->options([
                                'vol_serial' => 'بحث مجلد + متسلسل',
                                'vol_financial' => 'بحث مجلد + المالي',
                                'item_name' => 'بحث حسب اسم المادة',
                            ])
                            ->colors([
                                'vol_serial' => 'primary',
                                'vol_financial' => 'primary',
                                'item_name' => 'success',
                            ])
                            ->inline()
                            ->grouped()
                            ->default('vol_serial'),

                        Grid::make(2)->schema([
                            TextInput::make('year')
                                ->label('العام')
                                ->default(now()->year)
                                ->numeric()
                                ->columnSpan(1),

                            TextInput::make('reference_number')
                                ->label('مجلد')
                                ->columnSpan(1),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('financial_number')
                                ->label('رقم المالي')
                                ->columnSpan(1)
                                ->visible(fn ($get) => ($get('search_mode') ?? 'vol_serial') === 'vol_financial'),

                            TextInput::make('serial_number')
                                ->label('متسلسل')
                                ->columnSpan(1)
                                ->visible(fn ($get) => ($get('search_mode') ?? 'vol_serial') === 'vol_serial'),

                            TextInput::make('item_name_search')
                                ->label('اسم المادة')
                                ->placeholder('أدخل اسم المادة')
                                ->columnSpan(2)
                                ->visible(fn ($get) => ($get('search_mode') ?? 'vol_serial') === 'item_name'),
                        ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $mode = $data['search_mode'] ?? 'vol_serial';

                        $query->when(
                            $data['year'] ?? null,
                            fn (Builder $query, $year): Builder => $query->whereYear('date', $year)
                        );

                        if ($mode === 'vol_serial') {
                            $query
                                ->when($data['reference_number'] ?? null, fn($q, $v) => $q->where('reference_number', $v))
                                ->when($data['serial_number'] ?? null, fn($q, $s) => $q->where('bill_number', 'LIKE', "%{$s}%"));
                        }
                        elseif ($mode === 'vol_financial') {
                            $query
                                ->when($data['reference_number'] ?? null, fn($q, $v) => $q->where('reference_number', $v))
                                ->when($data['financial_number'] ?? null, fn($q, $f) => $q->where('financial_number', $f));
                        }
                        elseif ($mode === 'item_name') {
                            $query->when(
                                $data['item_name_search'] ?? null,
                                fn($q, $search) => $q->whereHas('billRecords.item', fn($q2) => $q2->where('name', 'LIKE', "%{$search}%"))
                            );
                        }

                        return $query;
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        $modeLabel = match ($data['search_mode'] ?? 'vol_serial') {
                            'vol_serial' => 'بحث مجلد + متسلسل',
                            'vol_financial' => 'بحث مجلد + المالي',
                            'item_name' => 'بحث حسب اسم المادة',
                            default => '',
                        };

                        if (!empty($data['reference_number']) || !empty($data['serial_number']) || !empty($data['financial_number']) || !empty($data['item_name_search'])) {
                            $indicators[] = 'النمط النشط: ' . $modeLabel;
                        }

                        if ($data['reference_number'] ?? null) {
                            $indicators[] = 'مجلد: ' . $data['reference_number'];
                        }
                        if ($data['serial_number'] ?? null) {
                            $indicators[] = 'متسلسل: ' . $data['serial_number'];
                        }
                        if ($data['financial_number'] ?? null) {
                            $indicators[] = 'الرقم المالي: ' . $data['financial_number'];
                        }
                        if ($data['item_name_search'] ?? null) {
                            $indicators[] = 'اسم المادة: ' . $data['item_name_search'];
                        }
                        return $indicators;
                    }),
            ])
             ->filtersFormColumns(2)
            ->persistFiltersInSession(true)
            ->actions([
                \Filament\Tables\Actions\ViewAction::make(),
                \Filament\Tables\Actions\EditAction::make(),

                \Filament\Tables\Actions\Action::make('approve')
                    ->label('اعتماد')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status === BillStatus::DRAFT->value)
                    ->action(function ($record) {
                        $record->update([
                            'status' => BillStatus::COMPLETED->value,
                            'approved_by' => filament()->auth()->id(),
                            'approved_at' => now(),
                        ]);

                        Notification::make()
                            ->title('تم اعتماد المذكرة بنجاح')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('اعتماد المذكرة')
                    ->modalDescription('هل أنت متأكد من اعتماد هذه المذكرة؟')
                    ->modalSubmitActionLabel('نعم، اعتمد')
                    ->modalCancelActionLabel('إلغاء'),

                \Filament\Tables\Actions\DeleteAction::make()
                    ->modalHeading('حذف المذكرة')
                    ->modalDescription('هل أنت متأكد من حذف هذه المذكرة؟')
                    ->modalSubmitActionLabel('نعم، احذف')
                    ->modalCancelActionLabel('إلغاء'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalHeading('حذف المذكرات المحددة')
                        ->modalDescription('هل أنت متأكد من حذف المذكرات المحددة؟')
                        ->modalSubmitActionLabel('نعم، احذف')
                        ->modalCancelActionLabel('إلغاء'),
                ]),
            ])
            ->defaultSort('date', 'desc')
            ->groups([
                Group::make('type')
                    ->label('حسب النوع')
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BillRecordsRelationManager::class,
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('كل المذكرات')
                ->icon('heroicon-o-document-text')
                ->url(static::getUrl('index'))
                ->group('المذكرات')
                ->sort(1),

            NavigationItem::make('استلام')
                ->icon('heroicon-o-shopping-cart')
                ->url(static::getUrl('index', ['type' => BillType::PURCHASE->value]))
                ->group('المذكرات')
                ->sort(2),

            NavigationItem::make('تسليم')
                ->icon('heroicon-o-arrow-path')
                ->url(static::getUrl('index', ['type' => BillType::TRANSFER->value]))
                ->group('المذكرات')
                ->sort(3),

            NavigationItem::make('تركيب وتنسيق')
                ->icon('heroicon-o-pencil-square')
                ->url(static::getUrl('index', ['type' => BillType::ADJUSTMENT->value]))
                ->group('المذكرات')
                ->sort(4),

            NavigationItem::make('إدخال')
                ->icon('heroicon-o-arrow-uturn-left')
                ->url(static::getUrl('index', ['type' => BillType::RETURN->value]))
                ->group('المذكرات')
                ->sort(5),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBills::route('/'),
            'create' => Pages\CreateBill::route('/create'),
            'edit' => Pages\EditBill::route('/{record}/edit'),
            'view' => Pages\ViewBill::route('/{record}'),
        ];
    }
}


 