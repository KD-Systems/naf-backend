<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PartStockAlertCollection extends JsonResource
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
            'name' => $this->part->aliases[0]->name,
            'unique_id' => $this->part->unique_id,
            'warehouse' => $this->warehouse->name,
            'unit_value' => $this->unit_value,
        ];
    }
}
