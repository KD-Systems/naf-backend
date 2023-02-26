<?php

namespace App\Mail\Invoice;

use App\Models\User;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceCreateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $invoice;
    public $authUser;
    public $emails;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice, User $authUser)
    {
        $this->invoice = $invoice;
        $this->authUser = $authUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = config('app.front_url') . "/panel/invoices/{$this->invoice->id}";

        return $this->subject('New Invoice Generated')
            ->markdown('emails.invoices.create', [
                'user' => $this->authUser,
                'invoice' => $this->invoice,
                'url' => $url
            ]);
    }
}
