<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationCommentResource extends JsonResource
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
            'quotaion_id' => $this->quotaion_id,
            'sender_id' => $this->sender_id,
            'text'=>$this->text,
            'type'=>$this->type,
            'remarks'=>$this->remarks,
            'status'=>$this->status,
        ];
    }
}
