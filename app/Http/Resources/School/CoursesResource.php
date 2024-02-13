<?php

namespace App\Http\Resources\School;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoursesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'years' => $this->years,
            'validity' => $this->validity,
            'certification' => $this->certification,
            'type' => $this->type,
            'course' => $this->course->name,
            // 'scholars' => $this->course->scholars->count(),
            'course_id' => $this->course->id,
            'prospectuses' => $this->prospectuses
        ];
    }
}
