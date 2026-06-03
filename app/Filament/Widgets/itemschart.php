<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
// use App\Models\Bill;
use App\Models\Item;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

// class itemschart extends ChartWidget
// {
//     protected static ?string $heading = 'المواد';

//     protected function getData(): array
//     {
//         $data = Trend::model(Item::class)
//             ->between(
//                 start: now()->startOfYear(),
//                 end: now()->endOfYear(),
//             )
//             ->perMonth()
//             ->count();
//         return [
//             'datasets' => [
//                 [
//                     'label' => 'مخطط المواد',
//                     'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
//                 ],
//             ],
//             'labels' => $data->map(fn (TrendValue $value) => $value->date),
//         ];
//     }

//     protected function getType(): string
//     {
//         return 'line';
//     }
// }
