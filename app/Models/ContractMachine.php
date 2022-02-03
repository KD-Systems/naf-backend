<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractMachine extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'machine_model_id',
        'mfg_number'
    ];

    protected $table = 'contract_machines';

    /**
     * Get the machines that owns the ContractMachine
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function machines()
    {
        return $this->belongsTo(MachineModel::class);
    }
}
