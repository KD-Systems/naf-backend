<?php

namespace App\Mail\Quotation;

use App\Models\User;
use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class QuotationCreateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $quotation;
    public $authUser;
    public $emails;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Quotation $quotation, User $authUser)
    {
        $this->quotation = $quotation;
        $this->authUser = $authUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = config('app.front_url') . "/panel/quotations/{$this->quotation->id}";

        return $this->subject('New Quotation Generated')
            ->markdown('emails.quotations.create', [
                'user' => $this->authUser,
                'quotation' => $this->quotation,
                'url' => $url
            ]);
    }
}
