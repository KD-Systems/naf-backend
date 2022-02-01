<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
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
            'company' => $this->company->only('id', 'name'),
            'machine' => $this->machine,
            'machine_model' => $this->machineModels,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_foc' => $this->is_foc,
            'status' => $this->status,
            'notes' => $this->notes,
            'has_expired' => $this->has_expired,
        ];
    }
}
