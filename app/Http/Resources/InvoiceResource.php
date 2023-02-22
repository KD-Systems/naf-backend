<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'company' => $this->company,
            'requisition' => $this->quotation->requisition,
            'part_items'=>$this->partItems,
            'invoice_number'=>$this->invoice_number,
            'invoice_date'=>$this->created_at,
            'payment_mode'=>$this->payment_mode,
            'payment_term'=>$this->payment_term,
            'payment_partial_mode'=>$this->payment_partial_mode,
            'next_payment'=>$this->next_payment,
            'last_payment'=>$this->last_payment,
            'payment_history'=>$this->paymentHistory,
            'is_delivered' => $this->deliveryNote ? true : false,
            'previous_due' => $this->previous_due,
            'created_by' => $this->created_by,
            'return_part' => $this->returnPart,
            // 'vat' => config('fixedData.vat_percent'),
            // 'vat_amount' => config('fixedData.vat_amount')
            'sub_total' => $this->sub_total,
            'vat' => $this->vat,
            'grand_total' => $this->grand_total,
            

        ];
    }
}
