<?php

namespace App\Models;

use App\Traits\LogPreference;
use App\Observers\StockObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartStock extends Model
{
    use HasFactory, SoftDeletes, LogPreference;

    protected $logName = 'stocks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'part_id',
        'warehouse_id',
        'box_heading_id',
        'unit_value',
        'stock_alert',
        'shipment_date',
        'shipment_invoice_no',
        'shipment_details',
        'yen_price',
        'formula_price',
        'selling_price',
        'notes'
    ];


    /**
     * The attributes that are contain dates.
     *
     * @var array<int, string>
     */
    public $dates = [
        'shipment_date',
    ];

    /**
     * Get the part that owns the PartStock
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    /**
     * Get the warehouse that owns the PartStock
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the box that has the stock stored
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function box()
    {
        return $this->belongsTo(BoxHeading::class, 'box_heading_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('unit_value', '>', 0);
    }
}
