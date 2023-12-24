<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyUserCollection extends JsonResource
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
            'avatar' => $this->avatar_url,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'phone' => $this->details->phone,
            'designation_id' => $this->details->designation_id,
            'designation_name' => $this->details?->designation?->name
        ];
    }
}
