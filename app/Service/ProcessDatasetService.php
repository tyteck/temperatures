<?php

declare(strict_types=1);

namespace App\Service;

use App\DataTransferObjects\DatasetDTO;
use App\DataTransferObjects\SingleDatasetDTO;
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
        DatasetDTO::from($this->jsonDataset)
            ->toCollection()
            ->each(function (SingleDatasetDTO $singleDatasetDTO): void {
                try {
                    $departement = Departement::query()->firstOrCreate(
                        ['code_insee' => $singleDatasetDTO->departement_code_insee],
                        ['nom' => $singleDatasetDTO->departement_nom],
                    );
                    Temperature::query()->upsert(
                        [
                            'date_observation' => $singleDatasetDTO->date_observation,
                            'departement_id' => $departement->id,
                            'temperature_moy' => $singleDatasetDTO->temperature_moy,
                            'temperature_min' => $singleDatasetDTO->temperature_min,
                            'temperature_max' => $singleDatasetDTO->temperature_max,
                        ],
                        ['departement_id', 'date_observation'],
                        ['temperature_moy', 'temperature_min', 'temperature_max']
                    );
                } catch (\Throwable $thrown) {
                    throw $thrown;
                }
            })
        ;
    }
}
