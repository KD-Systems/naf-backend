<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartAlias extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'part_id',
        'part_heading_id',
        'name',
        'part_number',
        'description',
    ];

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
}
