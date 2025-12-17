<?php

namespace App\Observers;

use App\Models\Estimate;

class EstimateObserver
{
    /**
     * Handle the Estimate "creating" event.
     */
    public function creating(Estimate $estimate): void
    {
        if (empty($estimate->number)) {
            $year = now()->year;
            $latest = Estimate::whereYear('date', $year)->latest()->first();
            $sequence = 1;

            if ($latest && preg_match('/-(\d+)$/', $latest->number, $matches)) {
                $sequence = intval($matches[1]) + 1;
            }

            $estimate->number = 'PREV-' . $year . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Handle the Estimate "updated" event.
     */
    public function updated(Estimate $estimate): void
    {
        //
    }

    /**
     * Handle the Estimate "deleted" event.
     */
    public function deleted(Estimate $estimate): void
    {
        //
    }

    /**
     * Handle the Estimate "restored" event.
     */
    public function restored(Estimate $estimate): void
    {
        //
    }

    /**
     * Handle the Estimate "force deleted" event.
     */
    public function forceDeleted(Estimate $estimate): void
    {
        //
    }
}
