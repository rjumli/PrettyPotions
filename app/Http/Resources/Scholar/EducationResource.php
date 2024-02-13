<?php

namespace App\Http\Resources\Scholar;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EducationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $info = json_decode($this->information);
        ($this->school != null) ? $this->school->scholar_id = $this->scholar_id : '';
        
        return [
            'id' => $this->id,
            'is_completed' => $this->is_completed,
            'school' => ($this->school == null) ? $info->school : new SchoolResource($this->school),
            'course' => ($this->course == null) ? $info->course : $this->course,
            'subcourse' => $this->subcourse,
            'level' => ($this->level == null) ? 'n/a' : $this->level,
            'award' => ($this->award == null) ? 'n/a' : $this->award,
            'graduated_year' => ($this->graduated_year == null) ? 'n/a' : $this->graduated_year,
            'info' => $info,
            'has_school' => ($this->school == null) ? false : true,
            'has_level' => ($this->level == null) ? false : true,
            'has_course' => ($this->course == null) ? false : true,
            'has_subcourse' => ($this->subcourse == null) ? false : true,
        ];
    }
}
