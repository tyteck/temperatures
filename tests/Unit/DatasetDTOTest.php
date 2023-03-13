<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DataTransferObjects\DatasetDTO;
use App\Exceptions\InvalidDatasetException;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class DatasetDTOTest extends TestCase
{
    /** @test */
    public function single_dataset_should_be_parsed_properly(): void
    {
        $dataset = File::get(fixtures_path('single_dataset.json'));

        $datasetDto = DatasetDTO::from($dataset);

        $this->assertInstanceOf(DatasetDTO::class, $datasetDto);

        $collection = $datasetDto->toCollection();
        $this->assertNotNull($collection);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(1, $collection);

        $this->assertEqualsCanonicalizing(
            $collection->first()->toArray(),
            [
                'date_observation' => Carbon::createFromFormat('Y-m-d', '2019-10-23')->toDateString(),
                'departement_code_insee' => 21,
                'departement_nom' => "Côte-d'Or",
                'temperature_max' => 18.7,
                'temperature_min' => 9.8,
                'temperature_moy' => 14.25,
            ]
        );
    }

    /** @test */
    public function small_dataset_should_be_parsed_properly(): void
    {
        $dataset = File::get(fixtures_path('small_dataset.json'));

        $datasetDto = DatasetDTO::from($dataset);

        $this->assertInstanceOf(DatasetDTO::class, $datasetDto);

        $collection = $datasetDto->toCollection();
        $this->assertNotNull($collection);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(17, $collection);

        $this->assertEqualsCanonicalizing(
            $collection->first()->toArray(),
            [
                'date_observation' => Carbon::createFromFormat('Y-m-d', '2019-10-22')->toDateString(),
                'departement_code_insee' => 29,
                'departement_nom' => 'Finistère',
                'temperature_max' => 15.48,
                'temperature_min' => 7.77,
                'temperature_moy' => 11.63,
            ]
        );

        $this->assertEqualsCanonicalizing(
            $collection->last()->toArray(),
            [
                'date_observation' => Carbon::createFromFormat('Y-m-d', '2019-10-23')->toDateString(),
                'departement_code_insee' => 48,
                'departement_nom' => 'Lozère',
                'temperature_max' => 11.9,
                'temperature_min' => 9.2,
                'temperature_moy' => 10.55,
            ]
        );
    }

    /** @test */
    public function empty_dataset_should_throw_exception(): void
    {
        $dataset = '';
        $this->expectException(InvalidDatasetException::class);
        $this->expectExceptionMessage('Dataset is empty.');
        DatasetDTO::from($dataset);
    }

    /*
        |--------------------------------------------------------------------------
        | helpers & providers
        |--------------------------------------------------------------------------
        */
}
