<?php

namespace App\Http\Resources;

use App\Models\Student;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Teacher;

class RegisterUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $teacher = Teacher::where('user_id', $this->id)->first();
        $student = Student::where('user_id', $this->id)->first();
        // echo $teacher;
        // echo $student;
        if (is_null($this)) {
            return null;
        }
        if (!is_null($student)) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'surname' => $this->surname,
                'email' => $this->email,
                'isAdmin' => (bool)$this->isAdmin,
                'isTeacher' => false,
                'isStudent' => true,
                'class_id' => $student->sclass_id
            ];
        }
        if (!is_null($teacher)) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'surname' => $this->surname,
                'email' => $this->email,
                'isAdmin' => (bool)$this->isAdmin,
                'isTeacher' => true,
                'isStudent' => false,
                'subject_id' => $teacher->subject_id
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'isAdmin' => (bool)$this->isAdmin,
            'isTeacher' => false,
            'isStudent' => false,
        ];
    }
}
