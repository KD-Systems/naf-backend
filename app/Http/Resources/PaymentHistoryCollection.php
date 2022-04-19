<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentHistoryCollection extends JsonResource
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
            // 'id' => $this->id,
            // 'company' => $this->company,
            // 'requisition' => $this->requisition,
            // 'part_items'=>$this->partItems,
            // 'pq_number'=>$this->pq_number
        ];
    }
}
