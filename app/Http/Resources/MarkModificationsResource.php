<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Mark;
use App\Http\Resources\MarksResource;
use App\Models\RegisterUser;

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
        $moderator = RegisterUser::where('id', $this->moderator_id)->first();
        if ($moderator != null) {
            $moderator_email = $moderator->email;
        } else {
            $moderator_email = null;
        }
        return [
            'id' => $this->id,
            'modification_datetime' => $this->modification_datetime,
            'moderator' => $this->moderator_id,
            'moderator_email' => $moderator_email,
            'mark_id' => $this->mark_id,
            // 'mark' => new MarksResource(Mark::where('id', $this->mark_id)->first()),
            'mark_before_modification' => $this->mark_before_modification,
            'mark_after_modification' => $this->mark_after_modification,
            'modification_reason' => $this->modification_reason
        ];
    }
}
