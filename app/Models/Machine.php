<?php

namespace App\Models;

use App\Traits\LogPreference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Machine extends Model
{
    use HasFactory,SoftDeletes,LogPreference;

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
     * The name of the logs to differentiate
     *
     * @var string
     */
    protected $logName = 'machines';


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
    public function headings()
    {
        return $this->hasMany(PartHeading::class);
    }
}
