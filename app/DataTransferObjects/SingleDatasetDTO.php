<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Exceptions\InvalidDatasetException;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @property string $code_insee_departement
 * @property string $departement
 * @property Carbon $date_observation
 * @property string $temperature_moy
 * @property string $temperature_min
 * @property string $temperature_max
 */
class SingleDatasetDTO implements Arrayable
{
    public string $departement_code_insee;
    public string $departement_nom;
    public Carbon $date_observation;
    public float $temperature_moy;
    public float $temperature_min;
    public float $temperature_max;

    private array $required = [
        'code_insee_departement',
        'departement',
        'date_obs',
        'tmoy',
        'tmin',
        'tmax',
    ];

    private function __construct(protected array $dataset)
    {
        try {
            throw_if(
                empty($this->dataset),
                new \Exception('Dataset is empty.')
            );

            array_map(function ($required): void {
                throw_unless(
                    isset($this->dataset['fields'][$required]),
                    new \Exception("Required key {$required} is missing.")
                );
            }, $this->required);

            $this->date_observation = Carbon::createFromFormat('Y-m-d', $this->dataset['fields']['date_obs']);
            $this->departement_code_insee = $this->dataset['fields']['code_insee_departement'];
            $this->departement_nom = $this->dataset['fields']['departement'];
            $this->temperature_max = floatval($this->dataset['fields']['tmax']);
            $this->temperature_min = floatval($this->dataset['fields']['tmin']);
            $this->temperature_moy = floatval($this->dataset['fields']['tmoy']);
        } catch (\Throwable $thrown) {
            throw new InvalidDatasetException($thrown->getMessage());
        }
    }

    public static function toObject(...$params)
    {
        return new static(...$params);
    }

    public function toArray(): array
    {
        return [
            'date_observation' => $this->date_observation->toDateString(),
            'departement_code_insee' => $this->departement_code_insee,
            'departement_nom' => $this->departement_nom,
            'temperature_max' => $this->temperature_max,
            'temperature_min' => $this->temperature_min,
            'temperature_moy' => $this->temperature_moy,
        ];
    }
}
