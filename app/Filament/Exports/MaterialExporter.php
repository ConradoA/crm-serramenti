<?php

namespace App\Filament\Exports;

use App\Models\Material;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class MaterialExporter extends Exporter
{
    protected static ?string $model = Material::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name')->label('name'),
            ExportColumn::make('category')->label('category'),
            ExportColumn::make('unit')->label('unit'),
            ExportColumn::make('cost_price')->label('cost_price'),
            ExportColumn::make('supplier_id')->label('supplier (ID)'),
            ExportColumn::make('supplier.name')->label('Supplier Name (Info Only)'),
            ExportColumn::make('code'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your material export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
