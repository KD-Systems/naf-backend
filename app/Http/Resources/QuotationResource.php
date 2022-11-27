<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationResource extends JsonResource
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
            'company' => $this->company,
            'requisition' => $this->requisition,
            'invoice' =>  $this->invoice?->invoice_number,
            'part_items' => $this->partItems,
            'pq_number' => $this->pq_number,
            'locked_at' => $this->locked_at,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'vat' => $this->requisition->vat = config('fixedData.vat_percent'),
            'vat_amount' => $this->requisition->vat_amount = config('fixedData.vat_amount')
        ];
    }
}
