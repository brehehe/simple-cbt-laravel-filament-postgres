<?php

namespace App\Http\Resources\General;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id'    => $this->id,
            'name'  => $this->name,
            'phone' => $this->phone ? intval($this->phone) : null,
            'email' => $this->email,
            'role'  => Str::title($this->roles()?->first()?->name),
        ];

        return $data;
    }
}
