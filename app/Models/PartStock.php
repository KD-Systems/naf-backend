<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartStock extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'part_id',
        'warehouse_id',
        'part_heading_id',
        'unit',
        'unit_value',
        'shipment_date',
        'shipment_invoice_no',
        'shipment_details',
        'yen_price',
        'formula_price',
        'selling_price'
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
     * Get the partHeading that owns the PartStock
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partHeading()
    {
        return $this->belongsTo(PartHeading::class);
    }
}
