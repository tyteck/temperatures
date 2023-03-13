<?php

declare(strict_types=1);

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Exceptions\InvalidDatasetException;
use App\Service\ProcessSingleDatasetService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ProcessSingleDatasetServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @test */
    public function dataset_should_be_recorded_properly(): void
    {
        $dataset = File::get(fixtures_path('single_dataset.json'));

        ProcessSingleDatasetService::create($dataset)->store();

        $this->assertDatabaseHas('temperatures', [
            'temperature_moy' => 14.25,
            'temperature_min' => 9.8,
            'date_observation' => Carbon::createFromFormat('Y-m-d', '2019-10-23'),
            'temperature_max' => 18.7,
        ]);

        $this->assertDatabaseHas('departements', [
            'nom' => "Côte-d'Or",
            'code_insee' => 21,
        ]);
    }

    /** @test */
    public function empty_dataset_should_throw_exception(): void
    {
        $dataset = '';
        $this->expectException(InvalidDatasetException::class);
        $this->expectExceptionMessage('Dataset is empty.');
        ProcessSingleDatasetService::create($dataset)->store();
    }

    /**
     * @dataProvider missingDataProvider
     *
     * @test
     */
    public function missing_data_in_dataset_should_throw_exception(string $dataset, string $required): void
    {
        $this->expectException(InvalidDatasetException::class);
        $this->expectExceptionMessage("Required key {$required} is missing.");
        ProcessSingleDatasetService::create($dataset)->store();
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
