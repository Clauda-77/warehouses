<?php


namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewBill extends ViewRecord
{
    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Action::make('print')
                ->label('طباعة الفاتورة')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn() => route('bill.pdf', $this->record->id))
                ->openUrlInNewTab(),

            Actions\Action::make('add_items')
                ->label('➕ إضافة/تعديل مواد')
                ->icon('heroicon-o-shopping-cart')
                ->color('primary')
                ->url(fn() => BillResource::getUrl('items', ['record' => $this->record->id])),
        ];
    }


 

    public function getContent()
    {
        return view('filament.resources.bill-resource.pages.view-bill', [
            'bill' => $this->record,
        ]);
    }
} 