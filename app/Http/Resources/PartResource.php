<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PartResource extends JsonResource
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
            'aliases' => $this->aliases,
            'descrption' => $this->descrption,
            'remarks' => $this->remarks,
            'image'=>$this->image,
            'barcode'=>$this->barcode,
            'updated_at' => $this->updated_at
        ];
    }
}
