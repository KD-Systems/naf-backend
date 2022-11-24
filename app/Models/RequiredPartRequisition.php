<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\RequiredPartRequisitionObserver;


class RequiredPartRequisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_id',
        'company_id',
        'engineer_id',
        'machine_id',
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
        'remarks',
        'created_by'
    ];

    protected $logName = 'required_part_requisitions';

    public static function boot()
    {
        parent::boot();
        self::creating(fn ($model) => $model->rr_number = 'RR' . date("Ym") . rand(10,100));
        self::observe(RequiredPartRequisitionObserver::class);
    }

    public function requisitions(){
        return $this->hasOne(Requisition::class,'id','requisition_id');
    }

    public function requiredPartItems()
    {
        return $this->hasMany(RequiredPartItems::class, 'required_requisition_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id', 'id');
    }

    public function machines()
    {
        return $this->belongsTo(CompanyMachine::class, 'machine_id', 'id');
    }

}
