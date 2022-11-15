<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionSummeryCollection extends JsonResource
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
            'invoice_number'=>$this->invoice_number,
            'company' => $this->company,
            'type'=>$this->quotation?->requisition?->type,
            'previous_due' => $this->previous_due,
            'totalAmount' =>$this->totalAmount,
            'totalPaid' =>$this->totalPaid,
            'due' => $this->previous_due?$this->previous_due - $this->totalPaid:$this->totalAmount - $this->totalPaid,


        ];

    }
}
