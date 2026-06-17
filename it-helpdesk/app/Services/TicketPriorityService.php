<?php

namespace App\Services;

class TicketPriorityService
{
    public function determine(string $impact, string $urgency): array
    {
        return match (true) {
            $impact === 'wide' && $urgency === 'high' => [
                'code' => 'P1',
                'label' => 'Kritis',
            ],
            $impact === 'wide' && $urgency === 'low' => [
                'code' => 'P2',
                'label' => 'Tinggi',
            ],
            $impact === 'narrow' && $urgency === 'high' => [
                'code' => 'P3',
                'label' => 'Sedang',
            ],
            default => [
                'code' => 'P4',
                'label' => 'Rendah',
            ],
        };
    }
}
