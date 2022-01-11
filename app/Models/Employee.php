<?php

namespace App\Models;

use App\Models\User;
use App\Models\Designation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'designation_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
}
