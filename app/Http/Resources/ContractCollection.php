<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractCollection extends JsonResource
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
            'company' => $this->company->only('id', 'name'),
            'machine' => $this->machine,
            'machine_model' => $this->machine_model,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_foc' => $this->is_foc,
            'status' => $this->status,
            'has_expired' => $this->has_expired,
        ];
    }
}
