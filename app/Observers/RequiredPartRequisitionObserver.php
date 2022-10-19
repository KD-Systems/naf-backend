<?php

namespace App\Observers;

use App\Models\RequiredPartRequisition;
use App\Models\User;
use App\Notifications\Requisition\RequiredRequisitionNotification;
use Illuminate\Support\Facades\Notification;

class RequiredPartRequisitionObserver
{
    /**
     * Handle the RequiredPartRequisition "created" event.
     *
     * @param  \App\Models\RequiredPartRequisition  $requiredPartRequisition
     * @return void
     */
    public function created(RequiredPartRequisition $RequiredPartRequisition)
    {
        // info($requiredPartRequisition);
        $userIds = explode(',', setting('notifiable_users'));
        $users = User::find($userIds);
        if ($users->count())
            Notification::send($users, new RequiredRequisitionNotification($RequiredPartRequisition, auth()->user()));

        $companyUsers = $RequiredPartRequisition->company->users()->active()->get();
        if ($companyUsers->count())
            Notification::send($companyUsers, new RequiredRequisitionNotification($RequiredPartRequisition, auth()->user()));
    }

    /**
     * Handle the RequiredPartRequisition "updated" event.
     *
     * @param  \App\Models\RequiredPartRequisition  $requiredPartRequisition
     * @return void
     */
    public function updated(RequiredPartRequisition $requiredPartRequisition)
    {
        //
    }

    /**
     * Handle the RequiredPartRequisition "deleted" event.
     *
     * @param  \App\Models\RequiredPartRequisition  $requiredPartRequisition
     * @return void
     */
    public function deleted(RequiredPartRequisition $requiredPartRequisition)
    {
        //
    }

    /**
     * Handle the RequiredPartRequisition "restored" event.
     *
     * @param  \App\Models\RequiredPartRequisition  $requiredPartRequisition
     * @return void
     */
    public function restored(RequiredPartRequisition $requiredPartRequisition)
    {
        //
    }

    /**
     * Handle the RequiredPartRequisition "force deleted" event.
     *
     * @param  \App\Models\RequiredPartRequisition  $requiredPartRequisition
     * @return void
     */
    public function forceDeleted(RequiredPartRequisition $requiredPartRequisition)
    {
        //
    }
}
