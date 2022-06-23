<?php

namespace App\Observers;

use App\Models\QuotationComment;
use App\Models\User;
use App\Notifications\Quotation\QuotationCommentCreateNotification;
use Illuminate\Support\Facades\Notification;

class QuotationCommentObserver
{
    /**
     * Handle the QuotationComment "created" event.
     *
     * @param  \App\Models\QuotationComment  $quotationComment
     * @return void
     */
    public function created(QuotationComment $quotationComment)
    {
        $userIds = explode(',', setting('notifiable_users'));
        $users = User::find($userIds);
        if ($users->count())
            Notification::send($users, new QuotationCommentCreateNotification($quotationComment, auth()->user()));
    }

    /**
     * Handle the QuotationComment "updated" event.
     *
     * @param  \App\Models\QuotationComment  $quotationComment
     * @return void
     */
    public function updated(QuotationComment $quotationComment)
    {
        //
    }

    /**
     * Handle the QuotationComment "deleted" event.
     *
     * @param  \App\Models\QuotationComment  $quotationComment
     * @return void
     */
    public function deleted(QuotationComment $quotationComment)
    {
        //
    }

    /**
     * Handle the QuotationComment "restored" event.
     *
     * @param  \App\Models\QuotationComment  $quotationComment
     * @return void
     */
    public function restored(QuotationComment $quotationComment)
    {
        //
    }

    /**
     * Handle the QuotationComment "force deleted" event.
     *
     * @param  \App\Models\QuotationComment  $quotationComment
     * @return void
     */
    public function forceDeleted(QuotationComment $quotationComment)
    {
        //
    }
}
