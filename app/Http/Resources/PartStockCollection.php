<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PartStockCollection extends JsonResource
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
            'warehouse' => $this->warehouse,
            'box' => $this->box,
            'unit' => $this->part->unit,
            'unit_value' => $this->unit_value,
            'yen_price' => $this->yen_price,
            'formula_price' => $this->formula_price,
            'selling_price' => $this->selling_price,
            'shipment_invoice' => $this->shipment_invoice_no,
            'arrival_date' => $this->shipment_date,
        ];
    }
}
