<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DataTransferObjects\SingleDatasetDTO;
use App\Exceptions\InvalidDatasetException;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Tests\Enums\FixtureFile;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class SingleDatasetDTOTest extends TestCase
{
    /** @test */
    public function dataset_should_be_recorded_properly(): void
    {
        $dataset = json_decode(File::get(FixtureFile::SINGLE_DATASET->path()), true);

        $singleDatasetDto = SingleDatasetDTO::toObject($dataset['records'][0]);

        $this->assertEqualsCanonicalizing(
            $singleDatasetDto->toArray(),
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
    public function empty_dataset_should_throw_exception(): void
    {
        $dataset = [];
        $this->expectException(InvalidDatasetException::class);
        $this->expectExceptionMessage('Dataset is empty.');
        SingleDatasetDTO::toObject($dataset);
    }

    /**
     * @dataProvider missingDataProvider
     *
     * @test
     */
    public function missing_data_in_dataset_should_throw_exception(string $dataset, string $required): void
    {
        $dataset = json_decode($dataset, true);
        $this->expectException(InvalidDatasetException::class);
        $this->expectExceptionMessage("Required key {$required} is missing.");
        SingleDatasetDTO::toObject($dataset);
    }

    /*
    |--------------------------------------------------------------------------
    | helpers & providers
    |--------------------------------------------------------------------------
    */

    public static function missingDataProvider(): array
    {
        return [
            'nom departement' => ['{"fields": { "tmoy": 14.25, "tmin": 9.8, "date_obs": "2019-10-23", "tmax": 18.7, "code_insee_departement": "77"}}', 'departement'],
            'code insee departement' => ['{"fields": { "tmoy": 14.25, "departement": "Seine et marne", "tmin": 9.8, "date_obs": "2019-10-23", "tmax": 18.7}}', 'code_insee_departement'],
            'temperature moy' => ['{"fields": { "departement": "Seine et marne", "tmin": 9.8, "date_obs": "2019-10-23", "tmax": 18.7, "code_insee_departement": "77"}}', 'tmoy'],
            'temperature min' => ['{"fields": { "tmoy": 14.25, "departement": "Seine et marne", "date_obs": "2019-10-23", "tmax": 18.7, "code_insee_departement": "77"}}', 'tmin'],
            'temperature max' => ['{"fields": { "tmoy": 14.25, "departement": "Seine et marne", "tmin": 9.8, "date_obs": "2019-10-23", "code_insee_departement": "77"}}', 'tmax'],
            'date observation' => ['{"fields": { "tmoy": 14.25, "departement": "Seine et marne", "tmin": 9.8, "tmax": 18.7, "code_insee_departement": "77"}}', 'date_obs'],
        ];
    }
}
