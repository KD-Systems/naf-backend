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
        ];
    }
}
