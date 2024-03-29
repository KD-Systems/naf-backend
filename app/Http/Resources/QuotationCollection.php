<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationCollection extends JsonResource
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
            'company' => $this->company,
            'requisition' => $this->requisition,
            'invoice' =>  $this->invoice?->invoice_number,
            'part_items'=>$this->partItems,
            'pq_number'=>$this->pq_number,
            'locked_at'=>$this->locked_at,
            'status'=>$this->status,
            'sub_total'=>$this->sub_total,
            'vat'=>$this->vat,
            'grand_total'=>$this->grand_total,
            'created_by'=>$this->user?->name,
            'sub_total' => $this->sub_total,
            'vat' => $this->vat,
            'grand_total' => $this->grand_total,
            'created_at' => $this->created_at,

        ];
    }
}
