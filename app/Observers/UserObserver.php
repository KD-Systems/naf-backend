<?php

namespace App\Observers;

use App\Models\User;
use Spatie\Activitylog\Contracts\Activity;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        //Log the activities
        activity()->causedBy($user->id)
            ->performedOn($user)
            ->log('created');
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //Log the activities
        if ($user->isDirty(['name', 'email', 'password', 'avatar']) || $user->status != boolVal($user->getOriginal()['status']))
            activity()
                ->causedBy(auth()->id())
                ->performedOn($user)
                ->withProperties(getDirtyFields($user, ['name', 'email', 'password', 'status', 'avatar']))
                ->log('updated');
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //Log the activities
        activity()
            ->causedBy(auth()->id())
            ->performedOn($user)
            ->log('deleted');
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //Log the activities
        activity()
            ->causedBy(auth()->id())
            ->performedOn($user)
            ->log('restored');
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
