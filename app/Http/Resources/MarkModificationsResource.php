<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Mark;

class MarkModificationsResource extends JsonResource
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
            'modification_datetime' => $this->modification_datetime,
            'moderator_id' => $this->moderator_id,
            'mark_id' => $this->mark_id,
            'mark_before_modification' => $this->mark_before_modification,
            'mark_after_modification' => $this->mark_after_modification,
            'modification_reason' => $this->modimodification_reason
        ];
    }
}
