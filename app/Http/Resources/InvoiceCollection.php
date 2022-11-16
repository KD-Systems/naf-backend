<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceCollection extends JsonResource
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
            'company' => $this->company,
            'deliveryNote'=> $this->deliveryNote, 
            'type'=>$this->quotation?->requisition?->type,
            'part_items'=>$this->partItems,
            'invoice_number'=>$this->invoice_number,
            'previous_due' => $this->previous_due,
            'totalAmount' => $this->totalAmount,
            'totalPaid' => $this->totalPaid,
        ];
    }
}
