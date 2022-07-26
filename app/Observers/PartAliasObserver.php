<?php

namespace App\Observers;

use App\Models\PartAlias;
use App\Models\OldPartNumber;

class PartAliasObserver
{
    /**
     * Handle the PartAlias "created" event.
     *
     * @param  \App\Models\PartAlias  $partAlias
     * @return void
     */
    public function created(PartAlias $partAlias)
    {
        //
    }

    /**
     * Handle the PartAlias "updated" event.
     *
     * @param  \App\Models\PartAlias  $partAlias
     * @return void
     */
    public function updated(PartAlias $partAlias)
    {
        $partAlias->isDirty('part_number') && $partAlias->oldPartNumbers()->create([
            'part_number' => $partAlias->getOriginal('part_number'),
            'machine_id' => $partAlias->machine_id
        ]);
    }

    /**
     * Handle the PartAlias "deleted" event.
     *
     * @param  \App\Models\PartAlias  $partAlias
     * @return void
     */
    public function deleted(PartAlias $partAlias)
    {
        //
    }

    /**
     * Handle the PartAlias "restored" event.
     *
     * @param  \App\Models\PartAlias  $partAlias
     * @return void
     */
    public function restored(PartAlias $partAlias)
    {
        //
    }

    /**
     * Handle the PartAlias "force deleted" event.
     *
     * @param  \App\Models\PartAlias  $partAlias
     * @return void
     */
    public function forceDeleted(PartAlias $partAlias)
    {
        //
    }
}
