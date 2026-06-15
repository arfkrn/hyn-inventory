<?php

namespace App\Observers;

use App\Models\Bahan;

class BahanObserver
{
    /**
     * Handle the bahans "created" event.
     */
    public function created(Bahan $bahans): void
    {
        cache()->forget('global_low_stock_count');
    }

    /**
     * Handle the bahans "updated" event.
     */
    public function updated(Bahan $bahans): void
    {
        cache()->forget('global_low_stock_count');
    }

    /**
     * Handle the bahans "deleted" event.
     */
    public function deleted(Bahan $bahans): void
    {
        //
    }

    /**
     * Handle the bahans "restored" event.
     */
    public function restored(Bahan $bahans): void
    {
        //
    }

    /**
     * Handle the bahans "force deleted" event.
     */
    public function forceDeleted(Bahan $bahans): void
    {
        //
    }
}
