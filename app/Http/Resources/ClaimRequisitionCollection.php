<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClaimRequisitionCollection extends JsonResource
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
            'id'                          => $this->id,
            'expected_delivery'           => $this->expected_delivery,
            'priority'                    => ucfirst($this->priority),
            'company'                     => $this->company,
            'quotation'                   => $this->quotation,
            'required_requisition_id'     => $this->requiredRequisition?->id,
            'required_requisition_number' => $this->requiredRequisition?->rr_number,
            'rq_number'                   => $this->rq_number,
            'machines'                    => $this->machines->pluck('model'),
            'status'                      => $this->status
        ];
    }
}
