<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\Pages;


use App\Filament\Resources\BillResource\RelationManagers\BillRecordsRelationManager;
use App\Models\Bill;
use App\Enums\BillType;
use App\Enums\BillStatus;
use Filament\Tables\Grouping\Group;
//use Filament\Forms\Components\Group;
use Filament\Navigation\NavigationItem;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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

                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn($state) => match ($state) {
                        BillStatus::DRAFT->value => 'مسودة',
                        'pending' => 'معلقة',
                        BillStatus::COMPLETED->value => 'مكتملة',
                        'cancelled' => 'ملغاة',
                        default => $state,
                    })
                    ->color(fn($state) => match ($state) {
                        BillStatus::DRAFT->value => 'gray',
                        'pending' => 'warning',
                        BillStatus::COMPLETED->value => 'success',
                        'cancelled' => 'default',
                        default => 'gray',
                    }),

                TextColumn::make('billRecords_count')
                    ->label('عدد الأصناف')
                    ->counts('billRecords')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('حالة المذكرة')
                    ->options([
                        BillStatus::DRAFT->value => 'مسودة',
                        BillStatus::PENDING->value => 'معلقة',
                        BillStatus::COMPLETED->value => 'مكتملة',
                        BillStatus::CANCELLED->value => 'ملغاة',
                    ])
                    ->placeholder('جميع الحالات'),
            ])
            ->actions([
                \Filament\Tables\Actions\ViewAction::make(),
                \Filament\Tables\Actions\EditAction::make(),

                \Filament\Tables\Actions\Action::make('manage_items')
                    ->label('إضافة/تعديل مواد')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('primary')
                    ->url(fn($record) => BillResource::getUrl('items', ['record' => $record])),

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

                 Group::make('status')
                    ->label('حسب الحالة')
                    ->collapsible(),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            BillRecordsRelationManager::class,
//            RelationManager::make('billRecords', BillRecordsRelationManager::class),
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
            'items' => Pages\ManageBillItems::route('/{record}/items'),
        ];
    }
}
//
//namespace App\Filament\Resources;
//
//use App\Filament\Resources\BillResource\Pages;
//use App\Models\Bill;
//
//use App\Enums\BillType;
//use App\Enums\BillStatus;
//
//use Filament\Navigation\NavigationItem;
//use Filament\Forms\Form;
//use Filament\Resources\Resource;
//use Filament\Tables;
//use Filament\Tables\Table;
//use Filament\Notifications\Notification;
//
//class BillResource extends Resource
//{
//
//    protected static ?string $model = Bill::class;
//    protected static ?string $navigationIcon = 'heroicon-o-document-text';
//    protected static ?string $navigationLabel = 'المذكرات';
//    protected static ?string $modelLabel = 'مذكرة';
//    protected static ?string $pluralModelLabel = 'المذكرات';
//    protected static ?string $activeNavigationIcon = 'heroicon-o-chevron-double-down';
//
//    protected static ?int $navigationSort = 2;
//
//    public static function form(Form $form): Form
//    {
//        return $form;
//
//    }
//
//    public static function table(Table $table): Table
//    {
//        return $table
//           ->modifyQueryUsing(fn ($query) => $query->when(request('type'), fn ($q, $type) => $q->where('type', $type)))
//
//            ->columns([
//                TextColumn::make('bill_number')
//                    ->label('رقم المذكرة')
//                    ->searchable()
//                    ->sortable(),
//
//                TextColumn::make('date')
//                    ->label('التاريخ')
//                    ->date('d/m/Y')
//                    ->sortable(),
//
//
//
//                TextColumn::make('party_name')
//                    ->label('الطرف')
//                    ->searchable()
//                    ->limit(25),
//
//                TextColumn::make('total')
//                    ->label('الإجمالي')
//                    ->money('SDG')
//                    ->sortable(),
//
//                Tables\Columns\BadgeColumn::make('status')
//                    ->label('الحالة')
//                    ->formatStateUsing(fn($state) => match ($state) {
//                        BillStatus::DRAFT->value => 'مسودة',
//                        'pending' => 'معلقة',
//                        BillStatus::COMPLETED->value => 'مكتملة',
//                        'cancelled' => 'ملغاة',
//                        default => $state,
//                    })
//                    ->color(fn($state) => match ($state) {
//                        BillStatus::DRAFT->value => 'gray',
//                        'pending' => 'warning',
//                        BillStatus::COMPLETED->value => 'success',
//                        'cancelled' => 'danger',
//                        default => 'gray',
//                    }),
//
//                TextColumn::make('billRecords_count')
//                    ->label('عدد الأصناف')
//                    ->counts('billRecords')
//                    ->sortable(),
//            ])
//            ->filters([
//                Tables\Filters\SelectFilter::make('type')
//                    ->label('نوع المذكرة')
//                    ->options([
//                        BillType::PURCHASE->value => 'شراء',
//                        BillType::TRANSFER->value => 'تحويل',
//                        BillType::ADJUSTMENT->value => 'تعديل',
//                        BillType::RETURN->value => 'مرتجع',
//                    ])
//                    ->placeholder('جميع الأنواع'),
//
//                Tables\Filters\SelectFilter::make('status')
//                    ->label('حالة المذكرة')
//                    ->options([
//                        BillStatus::DRAFT->value => 'مسودة',
//                        'pending' => 'معلقة',
//                        BillStatus::COMPLETED->value => 'مكتملة',
//                        'cancelled' => 'ملغاة',
//                    ])
//                    ->placeholder('جميع الحالات'),
//
//        ])
//
//
//
//            ->actions([
//                Tables\Actions\ActionGroup::make([
//                    Tables\Actions\ViewAction::make(),
//                    Tables\Actions\EditAction::make(),
//
//                    Tables\Actions\Action::make('manage_items')
//                        ->label('إضافة/تعديل مواد')
//                        ->icon('heroicon-o-shopping-cart')
//                        ->color('primary')
//                        ->url(fn($record) => BillResource::getUrl('items', ['record' => $record]))
//                        ->openUrlInNewTab(false),
//
//                    Tables\Actions\Action::make('approve')
//                        ->label('اعتماد')
//                        ->icon('heroicon-o-check-circle')
//                        ->color('success')
//                        ->visible(fn($record) => $record->status === BillStatus::DRAFT->value)
//                        ->action(function ($record) {
//                            $record->update([
//                                'status' => BillStatus::COMPLETED->value,
//                                'approved_by' => filament()->auth()->id(),
//                                'approved_at' => now(),
//                            ]);
//
//                            Notification::make()
//                                ->title('تم اعتماد المذكرة بنجاح')
//                                ->success()
//                                ->send();
//                        })
//                        ->requiresConfirmation()
//                        ->modalHeading('اعتماد المذكرة')
//                        ->modalDescription('هل أنت متأكد من اعتماد هذه المذكرة؟')
//                        ->modalSubmitActionLabel('نعم، اعتمد')
//                        ->modalCancelActionLabel('إلغاء'),
//
//                    Tables\Actions\DeleteAction::make()
//                        ->modalHeading('حذف المذكرة')
//                        ->modalDescription('هل أنت متأكد من حذف هذه المذكرة؟')
//                        ->modalSubmitActionLabel('نعم، احذف')
//                        ->modalCancelActionLabel('إلغاء'),
//                ]),
//            ])
//            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make()
//                        ->modalHeading('حذف المذكرات المحددة')
//                        ->modalDescription('هل أنت متأكد من حذف المذكرات المحددة؟')
//                        ->modalSubmitActionLabel('نعم، احذف')
//                        ->modalCancelActionLabel('إلغاء'),
//                ]),
//            ])
//            ->defaultSort('date', 'desc')
//            ->groups([
//                Tables\Grouping\Group::make('type')
//                    ->label('حسب النوع')
//                    ->collapsible(),
//
//                Tables\Grouping\Group::make('status')
//                    ->label('حسب الحالة')
//                    ->collapsible(),
//            ]);
//    }
//
//    // public static function getNavigationBadge(): ?string
//    // {
//    //     return static::getModel()::count();
//    // }
//
//    public static function getNavigationItems(): array
//{
//    return [
//        NavigationItem::make('كل المذكرات')
//            ->icon('heroicon-o-document-text')
//            ->url(static::getUrl('index'))
//            ->group('المذكرات')
//            ->sort(1),
//
//        NavigationItem::make('استلام')
//            ->icon('heroicon-o-shopping-cart')
//            ->url(static::getUrl('index', ['type' => BillType::PURCHASE->value]))
//            ->group('المذكرات')
//            ->sort(2),
//
//        NavigationItem::make('تسليم')
//            ->icon('heroicon-o-arrow-path')
//            ->url(static::getUrl('index', ['type' => BillType::TRANSFER->value]))
//            ->group('المذكرات')
//            ->sort(3),
//
//        NavigationItem::make('تركيب وتنسيق')
//            ->icon('heroicon-o-pencil-square')
//            ->url(static::getUrl('index', ['type' => BillType::ADJUSTMENT->value]))
//            ->group('المذكرات')
//            ->sort(4),
//
//        NavigationItem::make('إدخال')
//            ->icon('heroicon-o-arrow-uturn-left')
//            ->url(static::getUrl('index', ['type' => BillType::RETURN->value]))
//            ->group('المذكرات')
//            ->sort(5),
//    ];
//}
//
//    public static function getPages(): array
//    {
//        return [
//            // 'index' => Pages\ListBills::route('/'),
//            // 'create' => Pages\CreateBill::route('/create'),
//            // 'edit' => Pages\EditBill::route('/{record}/edit'),
//            // 'view' => Pages\ViewBill::route('/{record}'),
//            // 'items' => Pages\ManageBillItems::route('/{record}/items'),
//
//
//
//
//            'index' => Pages\ListBills::route('/'),
//            'create' => Pages\CreateBill::route('/create'),
//
//            'items' => Pages\AddBillItems::route('/{record}/items'),
//
//            'edit' => Pages\EditBill::route('/{record}/edit'),
//            'view' => Pages\ViewBill::route('/{record}'),
//        ];
//    }
//
//
//
//
//}
