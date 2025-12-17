<?php

namespace App\Observers;

use App\Models\EstimateItem;

class EstimateItemObserver
{
    /**
     * Handle the EstimateItem "saving" event.
     */
    public function saving(EstimateItem $estimateItem): void
    {
        $estimateItem->total_price = $estimateItem->quantity * $estimateItem->unit_price;
    }

    /**
     * Handle the EstimateItem "saved" event.
     */
    public function saved(EstimateItem $estimateItem): void
    {
        $estimateItem->estimate->recalculateTotals();
    }

    /**
     * Handle the EstimateItem "deleted" event.
     */
    public function deleted(EstimateItem $estimateItem): void
    {
        $estimateItem->estimate->recalculateTotals();
    }

    /**
     * Handle the EstimateItem "restored" event.
     */
    public function restored(EstimateItem $estimateItem): void
    {
        //
    }

    /**
     * Handle the EstimateItem "force deleted" event.
     */
    public function forceDeleted(EstimateItem $estimateItem): void
    {
        //
    }
}
