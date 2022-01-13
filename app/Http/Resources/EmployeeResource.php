<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'id' => $this->user->id,
            'avatar' => $this->user->avatar_url,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'status' => $this->user->status,
            'designation'=> $this->designation
        ];
    }
}
