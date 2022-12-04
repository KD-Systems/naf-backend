<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryNotesFocPartCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return parent::toArray($request);

        return [
            'id' => $this->id,
            'dn_number' => $this->dn_number,
            'part_items'=>$this->partItems,
        ];
    }
}
