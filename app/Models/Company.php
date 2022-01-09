<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'logo',
        'description'
    ];

    public function getLogoAttribute()
    {
        return $this->attributes['logo'] ?
            asset($this->attributes['logo']) :
            'https://ui-avatars.com/api/?name=' . \Str::slug($this->attributes['name']) . '&color=7F9CF5&background=EBF4FF';
    }
}
