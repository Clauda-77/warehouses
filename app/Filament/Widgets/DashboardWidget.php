<?php

namespace App\Filament\Widgets;

use App\Models\Bill;
// use App\Models\Customer;
use App\Models\Item;
use App\Models\Supplier;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            stat::make('اجمالي المذكرات :', Bill::count())
                ->description('عدد المذكرات التي تم ادخالها.')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->chart([15, 30, 22, 40, 25, 50])
                ->color('warning'),

            stat::make(' الموردين:', Supplier::count())
                ->description('الموردين.')
                ->descriptionIcon('heroicon-o-user')
                ->chart([10, 30, 33, 20, 15, 40])
                ->color('success'),

            stat::make(' إجمالي المواد :', Item::count())
                ->description(' المواد الموجودة في المستودع.')
                ->descriptionIcon('heroicon-o-presentation-chart-line')
                ->chart([0,50,33,25,77,39,54,80,90])
                ->color('primary'),
        ];
    }
}