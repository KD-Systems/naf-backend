<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = ['requisition_id','company_id','pq_number','locked_at','expriation_date','remarks'];

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
