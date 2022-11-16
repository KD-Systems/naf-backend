<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientPaymentHistoryDashboardCollection extends JsonResource
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
            'invoice_number' => $this->invoice_number,
            'quotation_number' => $this->quotation?->pq_number,
            'requistion_number' => $this->quotation?->requisition?->rq_number,
            'type' => $this->quotation?->requisition?->type,
            'previous_due' => $this->previous_due,
            'total_amount' => $this->totalAmount,
            'total_paid' => $this->totalPaid,
        ];

    }
}
