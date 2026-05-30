<?php

namespace App\View\Components;

use App\Models\Vehicle;
use Illuminate\View\Component;

class VehicleCard extends Component
{
    public function __construct(
        public Vehicle $vehicle,
        public bool $showPartner = true,
    ) {}

    public function render()
    {
        return view('components.vehicle-card');
    }
}
