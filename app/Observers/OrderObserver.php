<?php

namespace App\Observers;

use App\Models\orders;

class OrderObserver
{
    /**
     * Handle the orders "created" event.
     *
     * @param  \App\Models\orders  $orders
     * @return void
     */
    public function created(orders $orders)
    {
        //
    }

    /**
     * Handle the orders "updated" event.
     *
     * @param  \App\Models\orders  $orders
     * @return void
     */
    public function updated(orders $orders)
    {
        //
    }

    /**
     * Handle the orders "deleted" event.
     *
     * @param  \App\Models\orders  $orders
     * @return void
     */
    public function deleted(orders $orders)
    {
        //
    }

    /**
     * Handle the orders "restored" event.
     *
     * @param  \App\Models\orders  $orders
     * @return void
     */
    public function restored(orders $orders)
    {
        //
    }

    /**
     * Handle the orders "force deleted" event.
     *
     * @param  \App\Models\orders  $orders
     * @return void
     */
    public function forceDeleted(orders $orders)
    {
        //
    }
}
