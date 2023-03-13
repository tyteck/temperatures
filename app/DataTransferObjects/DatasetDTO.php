<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Exceptions\InvalidDatasetException;
use Illuminate\Support\Collection;

/**
 * @property Collection $datasetCollection
 * @property string     $jsonDataset
 */
class DatasetDTO
{
    protected Collection $datasetCollection;

    private function __construct(public string $jsonDataset)
    {
        $this->datasetCollection = collect();

        try {
            throw_if(
                empty($this->jsonDataset),
                new \Exception('Dataset is empty.')
            );

            $decoded = [];
            $temporary = json_decode($this->jsonDataset, true, flags: JSON_THROW_ON_ERROR);

            if (!isset($temporary[0])) {
                $decoded[] = $temporary;
            } else {
                $decoded = &$temporary;
            }

            array_map(function ($singleDataset): void {
                $this->datasetCollection->push(SingleDatasetDTO::toObject($singleDataset));
            }, $decoded);
        } catch (\Throwable $thrown) {
            throw new InvalidDatasetException($thrown->getMessage());
        }
    }

    public static function from(...$params)
    {
        return new static(...$params);
    }

    public function toCollection(): Collection
    {
        return $this->datasetCollection;
    }
}
