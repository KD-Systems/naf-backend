<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class UserResource extends JsonResource
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
            // 'designation'=>DesignationResource::make($this->employee->designation)
            // 'designation'=>$this->employee->designation,
            'permissions' => $this->roles()
                ->with('permissions')
                ->get()
                ->pluck('permissions')
                ->flatten()
                ->map(fn ($perm) => $perm->name),

            'details'=> $this->details,
        ];
    }
}
