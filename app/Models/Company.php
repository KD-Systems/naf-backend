<?php

namespace App\Models;

use App\Traits\LogPreference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Company extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, LogPreference, InteractsWithMedia;

          /**
     * The name of the logs to differentiate
     *
     * @var string
     */
    protected $logName = 'companies';

    protected $fillable = [
        'id',
        'name',
        'company_group',
        'machine_types',
        'address',
        'logo',
        'description',
        'tel',
        'email',
        'web',
        'trade_limit',
        'due_amount',
        'remarks'
    ];


    public $appends = ["logo_url"];

    public function getLogoUrlAttribute()
    {
        if(isset($this->attributes['logo']))
        return image($this->attributes['logo'], $this->attributes['name']);
        else
        return image('', $this->attributes['name']);
    }

    public function getMachineTypesAttribute()
    {
        return str_replace(',', ', ', $this->attributes['machine_types']);
    }

    /**
     * The users that belong to the Company
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'company_users')->withPivot('phone');
    }

    /**
     * Get all of the contracts for the Company
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get all of the machines for the Company
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function machines()
    {
        return $this->hasMany(CompanyMachine::class);
    }

    /**
     * Get all of the requisitions for the Company
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requisitions()
    {
        return $this->hasMany(Requisition::class);
    }

    public function requiredRequisitions()
    {
        return $this->hasMany(RequiredPartRequisition::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function  invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function  advancePaymentHistory()
    {
        return $this->hasMany(AdvancePaymentHistory::class);
    }


}
