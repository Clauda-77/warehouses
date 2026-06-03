<?php




namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use App\Enums\BillStatus;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditBill extends EditRecord
{
    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\Action::make('add_items')
//                ->label('➕ إضافة/تعديل مواد')
//                ->icon('heroicon-o-shopping-cart')
//                ->color('primary')
//                ->url(fn () => BillResource::getUrl('items', ['record' => $this->record->id])), // غير 'add-items' إلى 'items'

            Actions\Action::make('approve')
                ->label('اعتماد')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $this->record->status === BillStatus::DRAFT->value)
                ->action(function () {
                    $this->record->update([
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

            Actions\DeleteAction::make()
                ->modalHeading('حذف المذكرة')
                ->modalDescription('هل أنت متأكد من حذف هذه المذكرة؟')
                ->modalSubmitActionLabel('نعم، احذف')
                ->modalCancelActionLabel('إلغاء'),
        ];
    }
}

