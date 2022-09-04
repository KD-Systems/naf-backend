<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecentSaleCollection extends JsonResource
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

        // return  [
        //     'id' => $this->id,
        //     'part_id' => $this->stock?->part?->id,
        //     'company_name' => $this->company?->name,
        //     'unique_id' => $this->stock?->part?->unique_id,
        //     'name' => $this->stock?->part?->aliases[0]->name,
        // ];
    }
}
