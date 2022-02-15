<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MachineModelResource extends JsonResource
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
            'name' => $this->name,
            'space' => $this->space,
            'remarks' => $this->remarks,
            'description' => $this->description,
            'machine' => $this->machine->only('id', 'name'),
        ];
    }
}
