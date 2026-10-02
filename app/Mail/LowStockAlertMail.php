<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $business;
    public $items;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($business, $items)
    {
        $this->business = $business;
        $this->items = $items;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('⚠️ Low Stock Alert: ' . $this->business->name)
                    ->view('emails.low_stock_alert')
                    ->with([
                        'business' => $this->business,
                        'items' => $this->items,
                    ]);
    }
}
