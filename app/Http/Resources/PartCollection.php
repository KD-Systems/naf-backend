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
        $alias = $this->aliases->first();
        return [
            'id' => $this->id,
            'name' => $alias->name,
            'heading' => $alias->partHeading,
            'machine' => $alias->machine,
            'part_number' => $alias->part_number,
        ];
    }
}
