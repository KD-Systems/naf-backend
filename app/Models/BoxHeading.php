<?php

namespace App\Models;

use App\Traits\LogPreference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoxHeading extends Model
{
    use HasFactory, SoftDeletes, LogPreference;

    protected $logName = 'boxheading';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'unique_id',
        'barcode',
        'description'
    ];

    /**
     * The parts that belong to the BoxHeading
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function parts()
    {
        return $this->belongsToMany(Part::class, 'part_stocks', 'box_heading_id');
    }
}
