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
            'contracts' => $this->contracts->load('machine:id,name', 'machineModels'),
            'machines' => $this->contracts()
                ->active()
                ->with('machineModels', 'machineModels.machine')
                ->get()
                ->pluck('machineModels')
                ->flatten()
                ->unique('id'),
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at
        ];
    }
}
