<?php

namespace App\Models;

use App\Observers\QuotationObserver;
use App\Traits\LogPreference;
use App\Traits\NextId;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;

class Quotation extends Model implements HasMedia
{
    use HasFactory, LogPreference, NextId, InteractsWithMedia;


    protected $fillable = [
        'requisition_id',
        'company_id',
        'pq_number',
        'locked_at',
        'expriation_date',
        'sub_total',
        'grand_total',
        'vat',
        'discount',
        'vat_type',
        'discount_type',
        'remarks',
        'status',
        'created_by'
    ];

    /**
     * The name of the logs to differentiate
     *
     * @var string
     */
    protected $logName = 'quotations';

    public static function boot()
    {
        parent::boot();
        self::creating(fn ($model) => $model->pq_number = 'PQ' . random_int(
            100000,
            999999
        ));
        self::observe(QuotationObserver::class);
    }

    public function partItems()
    {
        return $this->morphMany(
            PartItem::class,
            'model'
        );
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

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
