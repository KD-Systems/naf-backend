<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyMachineForRequisitionCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        //return parent::toArray($request);

        return [
            'id' => $this->id,
            'contracts' => $this->contracts->map(fn ($perm) => [
                'is_foc' => $perm->is_foc,
                'machine_model' => $perm->machineModels->map(
                    fn ($c) =>
                    [
                        'machine_id' => $c->model?->machine?->id,
                        'machine_model_id' => $c->model?->id,
                        'Company_machine_id' => $c->id,
                        'name' => $c->model?->name, //machine model name
                    ])
            ]),

            'machine_model' => $this->machines->map(fn ($perm) => [
                'machine_id' => $perm?->model?->machine?->id,
                'machine_model_id' => $perm?->model?->id,
                'company_machine_id' => $perm?->id,
                'name' => $perm?->model?->name, //machine model name
            ]),

        ];
    }
}
