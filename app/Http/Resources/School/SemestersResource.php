<?php

namespace App\Http\Resources\School;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SemestersResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'academic_year' => $this->academic_year,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'semester' => $this->semester,
            'is_active' => $this->is_active
        ];
    }
}
