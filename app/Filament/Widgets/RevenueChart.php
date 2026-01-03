<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Fatturato Mensile';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 1;
    protected static ?string $maxHeight = '596px';

    protected function getData(): array
    {
        $year = $this->filter ?: now()->year;
        $start = Carbon::parse("{$year}-01-01")->startOfYear();
        $end = Carbon::parse("{$year}-01-01")->endOfYear();

        $data = Trend::model(Invoice::class)
            ->dateColumn('date')
            ->between(
                start: $start,
                end: $end,
            )
            ->perMonth()
            ->sum('amount'); // Sum 'amount' column

        return [
            'datasets' => [
                [
                    'label' => 'Fatturato (€)',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#10b981', // Emerald 500
                    'fill' => true,
                    'backgroundColor' => '#10b98120',
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            '2025' => '2025',
            '2026' => '2026',
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
