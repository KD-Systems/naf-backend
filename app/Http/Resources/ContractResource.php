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
        $arr = [
            'id' => $this->id,
            'company' => $this->company->only('id', 'name', 'logo_url'),
            'machine_models' => $this->machineModels,
            'is_foc' => $this->is_foc,
            'status' => $this->status,
            'has_expired' => $this->has_expired,
            'notes' => $this->notes
        ];
        if ($this->start_date) {
            $arr['start_date'] = $this->start_date;
        }
        if ($this->end_date) {
            $arr['end_date'] = $this->end_date;
        }
        return $arr;
    }
}
