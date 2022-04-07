<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'engineer_id',
        'priority',
        'type',
        'payment_mode',
        'expected_delivery',
        'payment_term',
        'payment_partial_mode',
        'partial_time',
        'next_payment',
        'ref_number',
        'machine_problems',
        'solutions',
        'reason_of_trouble',
        'remarks'
    ];

    /**
     * Get all of the partItems for the Requisition
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function partItems()
    {
        return $this->morphMany(PartItem::class, 'model');
    }

    /**
     * Get the company that owns the Requisition
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the engineer that owns the Requisition
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id', 'id');
    }

    /**
     * The machines that belong to the Requisition
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function machines()
    {
        return $this->belongsToMany(CompanyMachine::class, 'requisition_machines', 'requisition_id', 'machine_id');
    }
}
