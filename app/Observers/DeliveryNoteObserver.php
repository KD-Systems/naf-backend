<?php

namespace App\Observers;

use App\Models\DeliveryNote;
use App\Models\User;
use App\Notifications\DeliveryNote\DeliveryNoteCreateNotification;
use Illuminate\Support\Facades\Notification;

class DeliveryNoteObserver
{
    /**
     * Handle the DeliveryNote "created" event.
     *
     * @param  \App\Models\DeliveryNote  $deliveryNote
     * @return void
     */
    public function created(DeliveryNote $deliveryNote)
    {
        $userIds = explode(',', setting('notifiable_users'));
        $users = User::find($userIds);
        if ($users->count())
            Notification::send($users, new DeliveryNoteCreateNotification($deliveryNote, auth()->user()));
    }

    /**
     * Handle the DeliveryNote "updated" event.
     *
     * @param  \App\Models\DeliveryNote  $deliveryNote
     * @return void
     */
    public function updated(DeliveryNote $deliveryNote)
    {
        //
    }

    /**
     * Handle the DeliveryNote "deleted" event.
     *
     * @param  \App\Models\DeliveryNote  $deliveryNote
     * @return void
     */
    public function deleted(DeliveryNote $deliveryNote)
    {
        //
    }

    /**
     * Handle the DeliveryNote "restored" event.
     *
     * @param  \App\Models\DeliveryNote  $deliveryNote
     * @return void
     */
    public function restored(DeliveryNote $deliveryNote)
    {
        //
    }

    /**
     * Handle the DeliveryNote "force deleted" event.
     *
     * @param  \App\Models\DeliveryNote  $deliveryNote
     * @return void
     */
    public function forceDeleted(DeliveryNote $deliveryNote)
    {
        //
    }
}
