<?php

namespace App\Observers;

use App\Models\Coupon;

class CouponObserver
{

    public function updating(\App\Models\Coupon $coupon)
    {
        if (
            $coupon->usage_limit !== null &&
            $coupon->used_count >= $coupon->usage_limit
        ) {
            $coupon->is_active = false;
        }
    }

    /**
     * Handle the Coupon "created" event.
     */
    public function created(Coupon $coupon): void
    {
        //
    }

    /**
     * Handle the Coupon "updated" event.
     */
    public function updated(Coupon $coupon): void
    {
        //
    }

    /**
     * Handle the Coupon "deleted" event.
     */
    public function deleted(Coupon $coupon): void
    {
        //
    }

    /**
     * Handle the Coupon "restored" event.
     */
    public function restored(Coupon $coupon): void
    {
        //
    }

    /**
     * Handle the Coupon "force deleted" event.
     */
    public function forceDeleted(Coupon $coupon): void
    {
        //
    }
}
