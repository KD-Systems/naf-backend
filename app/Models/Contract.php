<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'machine_id',
        'machine_model_id',
        'start_date',
        'end_date',
        'status',
        'notes'
    ];

    public $dates = [
        'start_date',
        'end_date'
    ];

    /**
     * Get the status attribute based on end date and status field
     *
     * @return booean
     */
    public function getStatusAttribute()
    {
        return Carbon::create($this->attributes['end_date'])->gt(now()) && $this->attributes['status'];
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
}
