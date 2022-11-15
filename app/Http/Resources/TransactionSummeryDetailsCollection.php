<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionSummeryDetailsCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        // return parent::toArray($request); 

        return [
            'id' => $this->id,
            'invoice_id'=>$this->invoice_id,
            'invoice_number'=>$this->invoice?->invoice_number,
            'payment_mode' => $this->compapayment_modeny,
            'amount'=>$this->amount,
            'payment_date' => $this->payment_date,
            'remarks' =>$this->remarks,
            'created_by' =>$this->created_by,


        ];

    }
}
