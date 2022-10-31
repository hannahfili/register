<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\RegisterUser;
use App\Http\Resources\RegisterUserResource;

class StudentsCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $userData = RegisterUser::where('id', $this->user_id)->first();
        $userDataDTO = new RegisterUserResource($userData);
        return [
            'id' => $this->id,
            'user' => $userDataDTO
        ];
    }
}
