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
            'part_heading' => $this->partHeading,
            'unit' => $this->part->unit,
            'unit_value' => $this->unit_value,
            'shipment_invoice' => $this->shipment_invoice_no,
            'arrival_date' => $this->shipment_date,
        ];
    }
}
