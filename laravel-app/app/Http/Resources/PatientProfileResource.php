<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'phone' => $this->phone,
            'dob' => $this->dob?->format('Y-m-d'),
            'gender' => $this->gender,
            'insurance_number' => $this->insurance_number,
            'citizen_id' => $this->citizen_id,
            'address' => $this->address,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'medical_history' => $this->medical_history,
            'allergies' => $this->allergies,
        ];
    }
}
