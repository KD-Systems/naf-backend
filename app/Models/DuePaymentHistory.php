<?php

namespace App\Models;

use App\Traits\LogPreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DuePaymentHistory extends Model
{
    use HasFactory, SoftDeletes, LogPreference;

    protected $fillable = [
        'company_id',
        'amount',
        'invoice_number',
        'payment_method',
        'payment_method',
        'payment_details',
        'transaction_type',
        'is_returned',
        'created_by',
        'updated_by',
        'remarks',
    ];

    protected $casts = [
        'transaction_type' => 'boolean'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
