<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'description'
    ];

    /**
     * Get all of the partStocks for the Warehouse
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function partStocks()
    {
        return $this->hasMany(PartStock::class);
    }

    /**
     * The parts that belong to the Warehouse
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function parts()
    {
        return $this->belongsToMany(Part::class, 'part_stocks', 'warehouse_id', 'part_id');
    }
}
