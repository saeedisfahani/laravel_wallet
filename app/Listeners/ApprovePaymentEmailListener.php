<?php

namespace App\Listeners;

use App\Jobs\SendRejectPaymentNotify;
use App\Mail\RejectPaymentMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class ApprovePaymentEmailListener 
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        SendRejectPaymentNotify::dispatch($event->payment);
    }
}
