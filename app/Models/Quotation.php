<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = ['request_id','company_id','machine_id','pq_number','locked_at','expriation_date','remarks'];

    public function partItems()
    {
        return $this->morphMany(PartItem::class,'partItemable');
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
