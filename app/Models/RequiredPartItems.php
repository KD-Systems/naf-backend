<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequiredPartItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'required_requisition_id',
        'part_name',
        'part_number',
        'qty',
        'status',
        'remarks'
    ];

    protected $logName = 'required_part_items';
}
