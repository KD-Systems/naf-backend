<?php

namespace App\Models;

use App\Observers\InvoiceObserver;
use App\Traits\NextId;
use App\Traits\LogPreference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Invoice extends Model implements HasMedia 
{
    use HasFactory, LogPreference, NextId, InteractsWithMedia;

    protected $fillable = [
        'quotation_id',
        'company_id',
        'invoice_number',
        'expected_delivery',
        'payment_mode',
        'payment_term',
        'payment_partial_mode',
        'next_payment',
        'last_payment',
        'remarks',
        'status',
        'sub_total',
        'vat',
        'discount',
        'grand_total',
        'created_by'
    ];

    /**
     * The name of the logs to differentiate
     *
     * @var string
     */
    protected $logName = 'invoices';

    public static function boot()
    {
        parent::boot();
        self::creating(fn ($model) => $model->invoice_number = 'IN' . random_int(100000, 999999));
        self::observe(InvoiceObserver::class);
    }


    public function partItems()
    {
        return $this->morphMany(PartItem::class, 'model');
    }


    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    /**
     * Get all of the paymentHistory for the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentHistory()
    {
        return $this->hasMany(PaymentHistories::class);
    }

    /**
     * Get the deliveryNote associated with the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function deliveryNote()
    {
        return $this->hasOne(DeliveryNote::class);
    }

    public function returnPart()
    {
        return $this->hasOne(ReturnPart::class,'invoice_id','id');
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
