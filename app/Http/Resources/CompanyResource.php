<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'company_group' => $this->company_group,
            'machine_types' => $this->machine_types,
            'logo' => $this->logo_url,
            'description' => $this->description,
            'tel'=>$this->tel,
            'email'=>$this->email,
            'web'=>$this->web,
            'address'=>$this->address,
            'contracts' => $this->contracts->load('machinesInfo', 'machineModels.model.machine'),
            'machines' => $this->contracts()
                ->active()
                ->with('machineModels', 'machineModels.model')
                ->get()
                ->pluck('machineModels')
                ->flatten()
                ->unique('id'),
            'trade_limit' => $this->trade_limit,
            'due_amount' => $this->due_amount,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at
        ];
    }
}
