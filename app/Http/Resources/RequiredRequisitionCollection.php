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

        return parent::toArray($request);

        return [
            'id' => $this->id,
            'expected_delivery' => $this->expected_delivery,
            'priority' => ucfirst($this->priority),
            'company' => $this->company,
            'requisition' => $this->quotation,
            'rr_number'=>$this->rr_number,
            // 'machines' => $this->machines->pluck('model'),
            'status'=> $this->status
        ];

    }
}
