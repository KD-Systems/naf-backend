<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TopSellingCollection extends JsonResource
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
            'part_id' => $this->stock?->part?->id,
            'part_stock_id' => intval($this->part_stock_id),
            'totalSell' => intval($this->totalSell),
            'name' => $this->stock?->part?->aliases->pluck('name'),
        ];
    }
}
