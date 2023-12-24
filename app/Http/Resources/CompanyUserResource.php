<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyUserResource extends JsonResource
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
            'avatar' => $this->avatar_url,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->details->phone,
            'designation' => [
                'id' => $this->details->designation_id,
                'name' => $this->details?->designation?->name
            ],
            'status' => $this->status,
        ];
    }
}
