<?php

namespace App\Models;

use App\Observers\QuotationObserver;
use App\Traits\LogPreference;
use App\Traits\NextId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quotation extends Model
{
    use HasFactory, LogPreference, NextId;

    protected $fillable = ['requisition_id', 'company_id', 'pq_number', 'locked_at', 'expriation_date', 'remarks','status'];

    /**
     * The name of the logs to differentiate
     *
     * @var string
     */
    protected $logName = 'quotations';

    public static function boot()
    {
        parent::boot();
        self::creating(fn ($model) => $model->pq_number = 'PQ' . date("Ym") . self::getNextId());
        self::observe(QuotationObserver::class);
    }

    public function partItems()
    {
        return $this->morphMany(PartItem::class, 'model');
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
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
