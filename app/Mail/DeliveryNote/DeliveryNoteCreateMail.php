<?php

namespace App\Mail\DeliveryNote;

use App\Models\User;
use App\Models\DeliveryNote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeliveryNoteCreateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $deliveryNote;
    public $authUser;
    public $emails;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(DeliveryNote $deliveryNote, User $authUser)
    {
        $this->deliveryNote = $deliveryNote;
        $this->authUser = $authUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = config('app.front_url') . "/panel/delivery-notes/{$this->deliveryNote->id}";

        return $this->subject('New Delivery Note Generated')
            ->markdown('emails.deliveryNote.create', [
                'user' => $this->authUser,
                'deliveryNote' => $this->deliveryNote,
                'url' => $url
            ]);
    }
}
