<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyMachine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'machine_model_id',
        'mfg_number',
        'notes'
    ];

    /**
     * Get the machineModel that owns the CompanyMachine
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function machineModel()
    {
        return $this->belongsTo(MachineModel::class, 'machine_model_id', 'id');
    }

    /**
     * Get the company that owns the CompanyMachine
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
