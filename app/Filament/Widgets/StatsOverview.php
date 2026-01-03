<?php

namespace App\Filament\Widgets;

use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\WorkOrder;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Preventivi Approvati (30 gg)', Estimate::where('status', 'approved')->where('created_at', '>=', now()->subDays(30))->count())
                ->description('Preventivi confermati negli ultimi 30 giorni')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Commesse Attive', WorkOrder::whereIn('status', ['pending', 'working'])->count())
                ->description('Lavori in corso o in attesa')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('warning'),

            Stat::make('Fatturato (Anno)', '€ ' . number_format(Invoice::where('status', 'paid')->whereYear('date', now()->year)->sum('amount'), 2, ',', '.'))
                ->description('Totale incassato anno corrente')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Dummy trend for now
        ];
    }
}
