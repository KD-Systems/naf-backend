<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class YearlySalesReportResource extends JsonResource
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
        // return [
        //     'id' => $this->id,
        //     'company' => $this->company,
        //     'requisition'=>$this->quotation->requisition,
        //     'part_items'=>$this->partItems,
        //     'invoice_number'=>$this->invoice_number,
        //     'payment_mode'=>$this->payment_mode,
        //     'payment_term'=>$this->payment_term,
        //     'payment_partial_mode'=>$this->payment_partial_mode,
        //     'next_payment'=>$this->next_payment,
        //     'last_payment'=>$this->last_payment,
        //     'payment_history'=>$this->paymentHistory
        // ];
    }
}
