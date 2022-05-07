<?php

namespace App\Models;

use App\Traits\LogPreference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quotation extends Model
{
    use HasFactory,LogPreference;




    protected $fillable = ['requisition_id','company_id','pq_number','locked_at','expriation_date','remarks'];

         /**
     * The name of the logs to differentiate
     *
     * @var string
     */
    protected $logName = 'quotations';

    public function partItems()
    {
        return $this->morphMany(PartItem::class,'model');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
