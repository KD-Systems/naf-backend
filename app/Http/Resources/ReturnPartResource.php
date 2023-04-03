<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReturnPartResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

            return [
            'id' => $this->id,
            'tracking_number'=> $this->tracking_number, 
            'type'=>$this->type,
            'return_amount'=> $this->total,
            'created_at' => $this->created_at,
            'return_part_items' => $this->returnPartItems,
            'invoice_number' => $this->invoice->invoice_number,
            'Payment_mode' => $this->invoice->payment_mode,
            'invoice_subtotal' => $this->invoice->sub_total,
            'vat' => $this->invoice->vat,
            'discount' => $this->invoice->discount,
            'grand_total' => $this->invoice->grand_total,
            'invoice_date' => $this->invoice->created_at,
            'invoice_id' => $this->invoice->id,
        ];
    }
}
