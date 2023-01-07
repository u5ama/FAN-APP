<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return
            [
                'id'=>$this->id,
                'name'=>$this->name,
                'email'=>$this->email,
                'mobile_no'=>$this->mobile_no,
                'user_type'=>$this->user_type,
                'profile_pic'=>$this->profile_pic,
                'status'=>$this->status,
                'description'=>$this->description,
                'address'=>$this->address,
                'id_card_front'=>$this->id_card_front,
                'id_card_back'=>$this->id_card_back,
                'device_type'=>$this->device_type,
                'device_token'=>$this->device_token,
            ];
    }
}
