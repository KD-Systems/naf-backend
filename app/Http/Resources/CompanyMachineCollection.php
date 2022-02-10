<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyMachineCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'machine_model' => $this->machineModel->only('id', 'name'),
            'machine' => $this->machineModel->machine->only('id', 'name'),
            'mfg_number' => $this->mfg_number
        ];
    }
}
