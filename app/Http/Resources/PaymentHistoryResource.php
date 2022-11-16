<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentHistoryResource extends JsonResource
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
            'invoice' => $this->invoice,
            'payment_mode' => $this->payment_mode,
            'payment_date' => $this->payment_date,
            'transaction_details' => $this->transaction_details,
            'file' => asset('uploads/'.$this->file),
            'created_by' => $this->created_by,
            'amount' => $this->amount,
            'remarks'=> $this->remarks
        ];
    }
}
