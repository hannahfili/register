<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Student;
use App\Http\Resources\StudentsCollectionResource;

class SchoolClassesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $classStudents = Student::where('class_id', $this->id)->get();
        // RegisterUserResource::collection(RegisterUser::all());
        $classStudentsDTOs = StudentsCollectionResource::collection($classStudents);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'class_start' => $this->class_start,
            'class_end' => $this->class_end,
            'students' => $classStudentsDTOs
        ];
    }
}
