<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Company;
use App\Models\MachineModel;
use App\Traits\LogPreference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contract extends Model
{
    use HasFactory, SoftDeletes,LogPreference;

          /**
     * The name of the logs to differentiate
     *
     * @var string
     */
    protected $logName = 'contracts';

    protected $fillable = [
        'company_id',
        'machine_id',
        'is_foc',
        'start_date',
        'end_date',
        'status',
        'notes'
    ];

    public $dates = [
        'start_date',
        'end_date'
    ];

    public function scopeActive($q)
    {
        return $q->whereStatus(true);
    }

    /**
     * Get the status attribute based on end date and status field
     *
     * @return booean
     */
    public function getStatusAttribute()
    {
        return Carbon::create($this->attributes['end_date'])->endOfDay()->gt(now()) && $this->attributes['status'];
    }

    /**
     * Get the expiration status attribute based on end date field
     *
     * @return booean
     */
    public function getHasExpiredAttribute()
    {
        return Carbon::create($this->attributes['end_date'])->endOfDay()->lt(now());
    }

    /**
     * Get the company that owns the Contract
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the machine that owns the Contract
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    /**
     * Get the machineModel that owns the Contract
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function machineModels()
    {
        return $this->belongsToMany(CompanyMachine::class, 'contract_machines')->withPivot('mfg_number')->withTimestamps();
    }

    /**
     * Get all of the machinesInfo for the Contract
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function machinesInfo()
    {
        return $this->hasMany(ContractMachine::class);
    }
}
