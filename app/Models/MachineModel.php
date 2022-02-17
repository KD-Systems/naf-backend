<?php

namespace App\Models;

use App\Traits\LogPreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MachineModel extends Model
{
    use HasFactory, SoftDeletes, LogPreference;


          /**
     * The name of the logs to differentiate
     *
     * @var string
     */
    protected $logName = 'machine_models';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'machine_id',
        'name',
        'space',
        'description',
        'remarks',
    ];


    /**
     * Get the machine that owns the MachineModel
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
