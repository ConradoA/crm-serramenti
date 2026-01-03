<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\WorkOrderResource;
use App\Models\WorkOrder;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class WorkOrderCalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 1;

    public function fetchEvents(array $fetchInfo): array
    {
        return WorkOrder::query()
            ->whereNotNull('start_date')
            ->get()
            ->map(
                fn(WorkOrder $event) => [
                    'id' => $event->id,
                    'title' => "{$event->number} - {$event->client->name}",
                    'start' => $event->start_date->toIso8601String(),
                    'end' => $event->due_date?->toIso8601String(),
                    'url' => WorkOrderResource::getUrl('edit', ['record' => $event]),
                    'color' => match ($event->status) {
                        'pending' => 'gray',
                        'working' => '#eab308', // yellow-500
                        'completed' => '#22c55e', // green-500
                        'cancelled' => '#ef4444', // red-500
                        default => 'blue',
                    },
                ]
            )
            ->all();
    }
}
