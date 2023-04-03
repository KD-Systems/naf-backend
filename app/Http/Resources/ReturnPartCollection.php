<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReturnPartCollection extends JsonResource
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
            'invoice_number' => $this->invoice->invoice_number,
            'tracking_number'=> $this->tracking_number,
            'return_amount'=> $this->total,
            'type'=>$this->type,
            'created_at' => $this->created_at,
            'return_part_items' => $this->returnPartItems,
            'invoice_id' => $this->invoice->id
        ];
    }
}
