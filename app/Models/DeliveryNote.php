<?php

namespace App\Models;

use App\Traits\LogPreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    use HasFactory, LogPreference;

    protected $fillable = [
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

    public function partItems()
    {
        return $this->morphMany(PartItem::class, 'model');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
