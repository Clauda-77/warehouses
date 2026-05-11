<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Model;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'أصناف';
    protected static ?string $modelLabel = 'صنف';
    protected static ?string $pluralModelLabel = 'أصناف';
    protected static ?string $navigationGroup = 'التصنيفات';
    protected static ?string $activeNavigationIcon = 'heroicon-o-chevron-double-down';

    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('المعلومات الأساسية')
                    ->description('أدخل المعلومات الأساسية للفئة')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('اسم الفئة')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Set $set, $operation) {
                                        if ($operation === 'edit') return;

                                        if (!empty($state)) {
                                            $code = Str::slug(Str::substr($state, 0, 20), '_');
                                        $set('code', strtoupper($code));
                                        }
                                    })
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('code')
                                    ->label('كود الفئة')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50)
                                    ->placeholder('يتم إنشاؤه تلقائياً')
                                    ->disabled(fn ($operation) => $operation === 'edit')
                                    ->dehydrated()
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Select::make('parent_id')
                            ->label('الفئة الرئيسية')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('اختياري - للفئات الفرعية')
                            ->options(function ($record) {
                                $query = Category::query()->active();
                                if ($record) {
                                     $query->where('id', '!=', $record->id)
                                          ->whereNotIn('id', $record->getAllChildren()->pluck('id'));
                                }
                                return $query->pluck('name', 'id');
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                 if ($state) {
                                    $parent = Category::find($state);
                                    if ($parent) {
                                        $set('parent_info', "{$parent->code} - {$parent->name}");
                                    }
                                }
                            }),

                        Forms\Components\Placeholder::make('parent_info')
                            ->label('معلومات الفئة الرئيسية')
                            ->content(function (Get $get) {
                                $parentId = $get('parent_id');
                                if (!$parentId) return '---';

                                $parent = Category::find($parentId);
                                return $parent ? "الكود: {$parent->code} | الأصناف: {$parent->items_count}" : '---';
                            })
                            ->hidden(fn (Get $get) => !$get('parent_id')),

                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('الإحصائيات')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Placeholder::make('items_count')
                                    ->label('عدد الأصناف')
                                    ->content(fn ($record) => $record ? $record->items_count : '0')
                                    ->hidden(fn ($operation) => $operation === 'create'),

                                Forms\Components\Placeholder::make('children_count')
                                    ->label('عدد الفئات الفرعية')
                                    ->content(fn ($record) => $record ? $record->children()->count() : '0')
                                    ->hidden(fn ($operation) => $operation === 'create'),

                                Forms\Components\Placeholder::make('depth')
                                    ->label('مستوى العمق')
                                    ->content(fn ($record) => $record ? $record->depth : '0')
                                    ->hidden(fn ($operation) => $operation === 'create'),

                                Forms\Components\Placeholder::make('full_path')
                                    ->label('المسار الكامل')
                                    ->content(fn ($record) => $record ? $record->full_path : '---')
                                    ->hidden(fn ($operation) => $operation === 'create'),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->hidden(fn ($operation) => $operation === 'create'),

                Forms\Components\Section::make('الحالة')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('الفئة نشطة')
                            ->default(true)
                            ->inline(false)
                            ->helperText('الفئات غير النشطة لا تظهر في القوائم'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('الكود')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ الكود')
                    ->description(fn (Category $record): string =>
                        $record->depth > 0 ? str_repeat('-- ', $record->depth) . '↳' : '↳'
                    ),

                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الفئة')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Category $record): string =>
                        $record->parent ? 'تابعة لـ: ' . $record->parent->name : 'رئيسية'
                    ),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('الفئة الرئيسية')
                    ->placeholder('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('عدد الأصناف')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state): string => $state > 0 ? 'success' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('children_count')
                    ->label('الفئات الفرعية')
                    ->getStateUsing(fn (Category $record): int => $record->children()->count())
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state): string => $state > 0 ? 'primary' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            Tables\Filters\SelectFilter::make('parent_id')
                ->label('الفئة الرئيسية')
                ->relationship('parent', 'name')
                ->searchable()
                ->preload()
                ->options(function () {
                    return Category::query()->active()->pluck('name', 'id');
                })
                ->placeholder('جميع الفئات'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->placeholder('الكل')
                    ->trueLabel('نشط فقط')
                    ->falseLabel('غير نشط فقط'),

                Tables\Filters\Filter::make('has_parent')
                    ->label('الفئات الفرعية فقط')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('parent_id')),

                Tables\Filters\Filter::make('no_parent')
                    ->label('الفئات الرئيسية فقط')
                    ->query(fn (Builder $query): Builder => $query->whereNull('parent_id')),

                Tables\Filters\Filter::make('has_items')
                    ->label('تحتوي على أصناف')
                    ->query(fn (Builder $query): Builder => $query->where('items_count', '>', 0)),

                Tables\Filters\Filter::make('has_children')
                    ->label('لديها فئات فرعية')
                    ->query(fn (Builder $query): Builder => $query->whereHas('children')),

                Tables\Filters\Filter::make('no_items')
                    ->label('بدون أصناف')
                    ->query(fn (Builder $query): Builder => $query->where('items_count', 0)),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('view_items')
                        ->label('عرض الأصناف')
                        ->icon('heroicon-o-cube')
                        ->color('info')
                        ->url(fn (Category $record) => ItemResource::getUrl('index', [
                            'tableFilters[category_id][value]' => $record->id
                        ]))
                        ->visible(fn (Category $record) => $record->items_count > 0),

                    Tables\Actions\Action::make('view_children')
                        ->label('عرض الفئات الفرعية')
                        ->icon('heroicon-o-folder')
                        ->color('warning')
                        ->url(fn (Category $record) => static::getUrl('index', [
                            'tableFilters[parent_id][value]' => $record->id
                        ]))
                        ->visible(fn (Category $record) => $record->children()->count() > 0),

                    Tables\Actions\DeleteAction::make()
                        ->visible(fn (Category $record) => $record->can_delete),

                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                 Tables\Actions\DeleteBulkAction::make()
    ->visible(fn ($records) => $records?->every('can_delete') ?? false),

                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),

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

                    Tables\Actions\BulkAction::make('move_to_parent')
                        ->label('نقل إلى فئة رئيسية')
                        ->icon('heroicon-o-arrow-right')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('parent_id')
                                ->label('الفئة الرئيسية الجديدة')
                                ->options(Category::query()->active()->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->nullable(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update(['parent_id' => $data['parent_id']]);
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->reorderable('name')
            ->groups([
                Tables\Grouping\Group::make('parent.name')
                    ->label('حسب الفئة الرئيسية')
                    ->collapsible(),

                Tables\Grouping\Group::make('is_active')
                    ->label('حسب الحالة')
                    ->collapsible(),

                Tables\Grouping\Group::make('items_count')
                    ->label('حسب عدد الأصناف')
                    ->collapsible(),
            ])
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession();
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ChildrenRelationManager::class,
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['parent'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'الكود' => $record->code,
            'الفئة الرئيسية' => $record->parent?->name ?? '---',
            'الحالة' => $record->is_active ? 'نشط' : 'غير نشط',
            'الأصناف' => $record->items_count,
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return self::getUrl('view', ['record' => $record]);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'التصنيفات';
    }
}
