<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequiredRequisitionCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'requisition_id' => $this->requisition_id,
            'expected_delivery' => $this->expected_delivery,
            'priority' => ucfirst($this->priority),
            'company' => $this->company?->name,
            'type' => $this->type,
            'rr_number'=>$this->rr_number,
            'rq_number'=>$this->requisitions?->rq_number,
            // 'machines' => $this->machines->pluck('model'),
            'status'=> $this->status,
            'created_by'=>$this->user?->name,
            'created_at' => $this->created_at,

        ];

    }
}
