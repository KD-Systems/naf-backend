<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Requisition;
use App\Events\RequisitionCreated;
use App\Mail\Requisition\RequisitionCreateMail;
use App\Notifications\Requisition\RequisitionApproveNotification;
use App\Notifications\Requisition\RequisitionCreateNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class RequisitionObserver
{
    /**
     * Handle the Requisition "created" event.
     *
     * @param  \App\Models\Requisition  $requisition
     * @return void
     */
    public function created(Requisition $requisition)
    {
        $userIds = explode(',', setting('notifiable_users'));
        $users = User::find($userIds);
        if ($users->count())
            Notification::send($users, new RequisitionCreateNotification($requisition, auth()->user()));

        $companyUsers = $requisition->company->users()->active()->get();
        if ($companyUsers->count())
            Notification::send($users, new RequisitionCreateNotification($requisition, auth()->user()));

        $notifiableEmails = explode(',', setting('notifiable_emails'));
        if ($users->count())
            foreach ($notifiableEmails as $notifiableEmail)
                Mail::to($notifiableEmail)->send(new RequisitionCreateMail($requisition, auth()->user()));
    }

    /**
     * Handle the Requisition "updated" event.
     *
     * @param  \App\Models\Requisition  $requisition
     * @return void
     */
    public function updated(Requisition $requisition)
    {
        $userIds = explode(',', setting('notifiable_users'));
        $users = User::find($userIds);
        if ($users->count())
            Notification::send($users, new RequisitionApproveNotification($requisition, auth()->user()));
    }

    /**
     * Handle the Requisition "deleted" event.
     *
     * @param  \App\Models\Requisition  $requisition
     * @return void
     */
    public function deleted(Requisition $requisition)
    {
        //
    }

    /**
     * Handle the Requisition "restored" event.
     *
     * @param  \App\Models\Requisition  $requisition
     * @return void
     */
    public function restored(Requisition $requisition)
    {
        //
    }

    /**
     * Handle the Requisition "force deleted" event.
     *
     * @param  \App\Models\Requisition  $requisition
     * @return void
     */
    public function forceDeleted(Requisition $requisition)
    {
        //
    }
}
