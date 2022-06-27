<?php

namespace App\Models;

use App\Observers\QuotationCommentObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationComment extends Model
{
    use HasFactory;

    protected $fillable = ['quotation_id','sender_id','text','type','remarks'];

    public static function boot()
    {
        parent::boot();
        self::observe(QuotationCommentObserver::class);
    }

    /**
     * Get the user associated with the QuotationComment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
    /**
     * Get the user associated with the QuotationComment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
