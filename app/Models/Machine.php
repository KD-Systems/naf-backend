<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Machine extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'remarks'
    ];


    /**
     * Get all of the models for the Machine
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function models()
    {
        return $this->hasMany(MachineModel::class);
    }

    /**
     * Get all of the heading for the Machine
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function heading()
    {
        return $this->hasMany(PartHeading::class);
    }
}
