<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id','name','description'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
