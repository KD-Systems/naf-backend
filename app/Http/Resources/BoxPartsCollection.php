<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BoxPartsCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $alias = $this->aliases->first();
        return [
            'id' => $this->id,
            'image' => $this->image_url,
            'name' => $alias->name?? '--',
            'machines' => $this->machines->pluck('name')->implode(','),
            'part_number' => $alias->part_number,
            'unique_id'=> $this->unique_id,
            'arm'=>$this->arm,
            'unit'=>$this->unit
        ];
    }
}
