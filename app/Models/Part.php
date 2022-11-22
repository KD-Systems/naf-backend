<?php

namespace App\Models;

use App\Traits\LogPreference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Part extends Model
{
    use HasFactory, SoftDeletes, LogPreference;

    /**
     * The name of the logs to differentiate
     *
     * @var string
     */
    protected $logName = 'parts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $casts = [
        'is_foc' => 'boolean',
    ];

    protected $fillable = [
        'arm',
        'unique_id',
        'barcode',
        'image',
        'unit',
        'unit_value',
        'yen_price',
        'formula_price',
        'selling_price',
        'description',
        'remarks',
        'is_foc'
    ];

    public function getImageUrlAttribute()
    {
        return image($this->attributes['image'], 'Image');
    }

    /**
     * Get all of the alias for the Part
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aliases()
    {
        return $this->hasMany(PartAlias::class);
    }

    /**
     * The machines that belong to the Part
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function machines()
    {
        return $this->belongsToMany(Machine::class, 'part_aliases', 'part_id', 'machine_id');
    }

    /**
     * The partHeadings that belong to the Part
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function partHeadings()
    {
        return $this->belongsToMany(PartHeading::class, 'part_aliases', 'part_id', 'part_heading_id');
    }

    /**
     * Get all of the stocks for the Part
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stocks()
    {
        return $this->hasMany(PartStock::class);
    }

    /**
     * Get all of the oldPartNumbers for the PartAlias
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oldPartNumbers()
    {
        return $this->hasMany(OldPartNumber::class);
    }
}
