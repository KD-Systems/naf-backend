<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationComment extends Model
{
    use HasFactory;

    protected $fillable = ['quotation_id','sender_id','text','type','remarks'];

    /**
     * Get the user associated with the QuotationComment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function quotations()
    {
        return $this->hasOne(Quotation::class, 'quotation_id', 'id');
    }
}
