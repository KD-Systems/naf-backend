<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PartCollection extends JsonResource
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
            'image' => $this->image_url,
            'name' => $this->name,
            'heading' => $this->heading_name,
            'machines' => $this->machines,
            'part_number' => $this->part_number,
            'old_part_number' => $this->old_part_number,
            'unique_id'=> $this->unique_id,
            'arm'=>$this->arm,
            'unit'=>$this->unit,
            'formula_price'=>$this->formula_price,
            'selling_price'=>$this->selling_price,
            'stocks'=>$this->stocks,
            'remarks'=>$this->remarks,

        ];
    }
}
