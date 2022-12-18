<?php

namespace App\Http\Resources;

use App\Models\RegisterUser;
use App\Models\Student;
use App\Http\Resources\RegisterUserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MarksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $student = RegisterUser::where('id', $this->user_student_id)->first();
        $moderator = RegisterUser::where('id', $this->moderator_id)->first();
        $phpdate = strtotime($this->updated_at);
        $mysqldate = date('Y-m-d H:i:s', $phpdate);
        if ($mysqldate == $this->mark_datetime) {
            return [
                'id' => $this->id,
                'student' => new RegisterUserResource($student),
                'subject' => $this->subject,
                'moderator' => new RegisterUserResource($moderator),
                'activity' => $this->activity,
                'mark_datetime' => $this->mark_datetime,
                'value' => $this->value,
                'updated_at' => ""
            ];
        }
        return [
            'id' => $this->id,
            'student' => new RegisterUserResource($student),
            'subject' => $this->subject,
            'moderator' => new RegisterUserResource($moderator),
            'activity' => $this->activity,
            'mark_datetime' => $this->mark_datetime,
            'value' => $this->value,
            'updated_at' => $mysqldate
        ];
    }
}
