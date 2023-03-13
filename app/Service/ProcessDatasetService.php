<?php

declare(strict_types=1);

namespace App\Service;

use App\DataTransferObjects\DatasetDTO;
use App\Models\Departement;
use App\Models\Temperature;
use Illuminate\Support\Collection;

class ProcessDatasetService
{
    protected Collection $dataset;

    private function __construct(public string $jsonDataset)
    {
    }

    public static function from(...$params)
    {
        return new static(...$params);
    }

    public function store(): void
    {
        try {
            $datasetObjects = DatasetDTO::toCollection($this->jsonDataset);
            
            if ($this->departement === null) {
                $departement = Departement::query()->firstOrCreate(
                    ['code_insee' => $datasetObject->departement_code_insee],
                    ['nom' => $datasetObject->departement_nom],
                );
            }

            Temperature::create(
                [
                    'date_observation' => $datasetObject->date_observation,
                    'departement_id' => $departement->id,
                    'temperature_moy' => $datasetObject->temperature_moy,
                    'temperature_min' => $datasetObject->temperature_min,
                    'temperature_max' => $datasetObject->temperature_max,
                ]
            );
        } catch (\Throwable $thrown) {
            throw $thrown;
        }
    }
}
