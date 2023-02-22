<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_id',
        'model_type',
        'model_id',
        'quantity',
        'unit_value',
        'total_value',
        'vat',
        'discount',
        'others',
        'status',
        'remarks',
        'type'
    ];

    // public $appends = ["date"];

    // public function getDateAttribute()
    // {
    //     return Carbon::parse($this->attributes['created_at'])->format('Y-d-m');
    // }

    public $casts = [
        'part_id' => 'integer'
    ];

    /**
     * Get the Part that owns the PartItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Part()
    {
        return $this->belongsTo(Part::class);
    }

    public function partAliases()
    {
        return $this->belongsTo(PartAlias::class);
    }
}
