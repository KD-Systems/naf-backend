<?php

namespace App\Models;

use App\Traits\NextId;
use App\Traits\LogPreference;
use Spatie\MediaLibrary\HasMedia;
use App\Events\RequisitionCreated;
use App\Observers\RequisitionObserver;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requisition extends Model implements HasMedia
{
    use HasFactory, LogPreference, NextId, InteractsWithMedia;

    protected $fillable = [
        'company_id',
        'engineer_id',
        'priority',
        'type',
        'payment_mode',
        'account_details',
        'expected_delivery',
        'payment_term',
        'payment_partial_mode',
        'partial_time',
        'next_payment',
        'ref_number',
        'machine_problems',
        'solutions',
        'reason_of_trouble',
        'rq_number',
        'status',
        'is_company',
        'remarks',
        'created_by'
    ];

    /**
     * The name of the logs to differentiate
     *
     * @var string
     */
    protected $logName = 'requisitions';

    public static function boot()
    {
        parent::boot();
        self::creating(fn ($model) => $model->rq_number = 'RQ' . random_int(100000, 999999));
        self::observe(RequisitionObserver::class);
    }

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

    /**
     * Get the user associated with the Requisition
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function quotation()
    {
        return $this->hasOne(Quotation::class, 'requisition_id', 'id');
    }

    public function requiredRequisition(){
        return $this->hasOne(RequiredPartRequisition::class,'requisition_id','id');
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'created_by');
    }

}
