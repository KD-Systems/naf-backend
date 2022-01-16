<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'company_group',
        'machine_types',
        'logo',
        'description'
    ];

    public function getLogoUrlAttribute()
    {
        return image($this->attributes['logo']);
    }

    public function getMachineTypesAttribute()
    {
        return str_replace(',', ', ',$this->attributes['machine_types']);
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
}
