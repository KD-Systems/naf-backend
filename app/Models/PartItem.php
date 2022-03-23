<?php

namespace App\Models;

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
        'remarks'
    ];


    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Get the Part that owns the PartItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Part()
    {
        return $this->belongsTo(Part::class);
    }
}
