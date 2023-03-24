<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemperatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codeDepartement' => $this->departement->code_insee,
            'departement' => $this->departement->nom,
            'temperatureMoyenne' => $this->temperature_moy,
            'temperatureMinimum' => $this->temperature_min,
            'temperatureMaximum' => $this->temperature_max,
            'dateObservation' => $this->date_observation,
        ];
    }
}
