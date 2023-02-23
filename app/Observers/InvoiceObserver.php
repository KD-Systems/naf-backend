<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\User;
use App\Notifications\Invoice\InvoiceCreateNotification;
use Illuminate\Support\Facades\Notification;
use App\Mail\invoice\InvoiceCreateMail;
use Illuminate\Support\Facades\Mail;

class InvoiceObserver
{
    /**
     * Handle the Invoice "created" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function created(Invoice $invoice)
    {
        $userIds = explode(',', setting('notifiable_users'));
        $users = User::find($userIds);
        if ($users->count())
            Notification::send($users, new InvoiceCreateNotification($invoice, auth()->user()));

        $companyUsers = $invoice->company->users()->active()->get();
        if ($companyUsers->count())
            Notification::send($companyUsers, new InvoiceCreateNotification($invoice, auth()->user()));
            
        $notifiableEmails = explode(',', setting('notifiable_emails'));
        $notifiableEmails = array_filter($notifiableEmails);
        if (count($notifiableEmails))
            foreach ($notifiableEmails as $notifiableEmail)
                Mail::to($notifiableEmail)->send(new InvoiceCreateMail($invoice, auth()->user()));
    }

    /**
     * Handle the Invoice "updated" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function updated(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "deleted" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function deleted(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "restored" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function restored(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "force deleted" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function forceDeleted(Invoice $invoice)
    {
        //
    }
}
