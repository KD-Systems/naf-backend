<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Part extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'remarks'
    ];

    /**
     * Get all of the alias for the Part
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aliases()
    {
        return $this->hasMany(PartAlias::class);
    }
}
