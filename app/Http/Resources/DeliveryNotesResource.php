<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryNotesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable 
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'dn_number' => $this->dn_number,
            'remarks'=>$this->remarks,
            'invoice'=>$this->invoice,
            'company' => $this->invoice->company,
            'requisition'=>$this->invoice->quotation->requisition,
            'part_items'=>$this->invoice->quotation->partItems,
            'delivery_date'=>$this->created_at,
        ];
    }
}
