<?php

namespace App\Models;

use App\Observers\PartAliasObserver;
use App\Traits\LogPreference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartAlias extends Model
{
    use HasFactory, SoftDeletes,LogPreference;

    protected $logName = 'alias';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'part_id',
        'machine_id',
        'part_heading_id',
        'name',
        'part_number',
        'old_part_number',
        'description',
    ];

    public static function boot()
    {
        parent::boot();
        self::observe(PartAliasObserver::class);
    }

    /**
     * Get the part that owns the PartAlias
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    /**
     * Get the part that owns the PartAlias
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partHeading()
    {
        return $this->belongsTo(PartHeading::class);
    }

    /**
     * Get the machine that owns the PartAlias
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    /**
     * Get all of the oldPartNumbers for the PartAlias
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oldPartNumbers()
    {
        return $this->hasMany(OldPartNumber::class, 'part_id', 'part_id');
    }
}
