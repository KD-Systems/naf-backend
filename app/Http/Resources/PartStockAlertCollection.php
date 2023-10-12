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
            'part_id' => $this->part_id,
            'name' => $this->part->aliases[0]->name ?? "N/A",
            'part_number' => $this->part->aliases[0]->part_number ?? "N/A",
            'unique_id' => $this->part->unique_id ?? "N/A",
            'warehouse' => $this->warehouse->name ?? "N/A",
            'unit_value' => $this->unit_value,
        ];
    }
}
