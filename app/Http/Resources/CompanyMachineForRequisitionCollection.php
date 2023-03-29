<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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

        return [
            'id' => $this->id,
            'contracts'     => $this->contracts->map(fn ($perm) => [
                'is_foc'    => $perm->is_foc,
                'is_status'    => $perm->status,
                'is_expired'  => (Carbon::now() > $perm->end_date) ? true : false,
                'machine_model' => $perm->machineModels->map(
                    fn ($c) =>
                    [
                        'Company_machine_id'    => $c->id,
                        'machine_id'            => $c->model?->machine?->id,
                        'machine_model_id'      => $c->model?->id,
                        'name'                  => $c->model?->name, //machine model name
                    ]
                )
            ]),

            'machine_model' => $this->machines->map(fn ($perm) => [
                'company_machine_id'    => $perm?->id,
                'machine_id'            => $perm?->model?->machine?->id,
                'machine_model_id'      => $perm?->model?->id,
                'name'                  => $perm?->model?->name, //machine model name
            ]),

        ];
    }
}
