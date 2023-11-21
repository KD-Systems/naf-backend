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
            'sub_total' => $this->sub_total,
            'vat' => $this->vat,
            'discount' => $this->discount,
            'vat_type' => $this->vat_type,
            'discount_type' => $this->discount_type,
            'grand_total' => $this->grand_total,
            'created_by' => $this->user?->name,
            'created_at'=>$this->created_at,
        ];
    }
}
