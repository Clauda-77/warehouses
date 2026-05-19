<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Resources\ItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;


class EditItem extends EditRecord
{
    protected static string $resource = ItemResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //     ];
    // }


    protected function getHeaderActions(): array
    {
        return [
             
            Actions\Action::make('print')
                ->label('طباعة البطاقة')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('items.print', $this->record->id))
                ->openUrlInNewTab(),
        ];
    }
        protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('تم التعديل بنجاح.')
            ->icon('heroicon-o-information-circle')
            ->color('success');
    }
}
