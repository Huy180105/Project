<?php

namespace App\Http\Resources;

use App\Models\QueueTicket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'room_number' => $this['room_number'],
            'current_number' => $this['current_number'],
            'average_time_per_patient' => $this['average_time_per_patient'],
        ];
    }
}
