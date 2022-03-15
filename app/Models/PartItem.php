<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartItem extends Model
{
    use HasFactory;

    protected $fillable = ['part_id','model_type','model_id','quantity','unit_value','remarks'];


    public function partItemable(){
        return $this->morphTo();
    }

}
