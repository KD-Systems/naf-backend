<?php

namespace App\Models;

use App\Observers\DeliveryNoteObserver;
use App\Traits\NextId;
use App\Traits\LogPreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    use HasFactory, LogPreference,NextId;

    protected $fillable = [
        'company_id',
        'invoice_id',
        'dn_number',
        'remarks',
        'created_at'
    ];

    /**
     * The name of the logs to differentiate
     *
     * @var string
     */
    protected $logName = 'delivery_notes';

    public static function boot()
    {
        parent::boot();
        self::creating(fn ($model) => $model->dn_number = 'DN' . random_int(100000, 999999));
        self::observe(DeliveryNoteObserver::class);
    }

    public function partItems()
    {
        return $this->morphMany(PartItem::class, 'model');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
