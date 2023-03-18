<?php

declare(strict_types=1);

namespace Tests\Feature\Commands;

use App\Console\Commands\ObtainTemperatures;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;
use Tests\Traits\MockOdreApi;

/**
 * @internal
 *
 * @coversNothing
 */
class ObtainTemperaturesTest extends TestCase
{
    use LazilyRefreshDatabase;
    use MockOdreApi;

    /** @test */
    public function command_before_january_2018_should_fail(): void
    {
        $this->artisan('temperatures:get', ['since' => '2017-01-01'])
            ->assertFailed()
            ->expectsOutputToContain('ODRE api is only available from January 2018 to the end of previous month.')
        ;
    }

    /** @test */
    public function command_to_today_should_fail(): void
    {
        $since = now()->subdays(15);
        $to = now();
        $this->artisan(
            'temperatures:get',
            [
                'since' => $since->format(ObtainTemperatures::PERIOD_FORMAT),
                'to' => $to->format(ObtainTemperatures::PERIOD_FORMAT),
            ]
        )
            ->assertFailed()
            ->expectsOutputToContain('ODRE api is only available from January 2018 to the end of previous month.')
        ;
    }

    /** @test */
    public function command_with_valid_arguments_and_single_dataset_should_run_smoothly(): void
    {
        // only to be "consistent" with fixture data
        $dayToObtain = Carbon::createFromFormat('Y-m-d', '2019-10-23');

        // Faking ODRE api with single dataset
        $this->fakeSingleDataset();

        $this->artisan(
            'temperatures:get',
            [
                'since' => $dayToObtain->format(ObtainTemperatures::PERIOD_FORMAT),
                'to' => $dayToObtain->format(ObtainTemperatures::PERIOD_FORMAT),
            ]
        )
            ->assertSuccessful()
        ;

        $this->assertDatabaseCount('temperatures', 1);
        $this->assertDatabaseCount('departements', 1);

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
    public function command_with_valid_arguments_and_small_dataset_should_run_smoothly(): void
    {
        // 2022-12-01 TO 2023-02-01
        $since = Carbon::createFromFormat('Y-m-d', '2019-10-22');
        $to = Carbon::createFromFormat('Y-m-d', '2019-10-22');
        $this->fakeSmallDataset();

        $this->artisan(
            'temperatures:get',
            [
                'since' => $since->format(ObtainTemperatures::PERIOD_FORMAT),
                'to' => $to->format(ObtainTemperatures::PERIOD_FORMAT),
            ]
        )
            ->assertSuccessful()
        ;

        $this->assertDatabaseCount('temperatures', 17);
        $this->assertDatabaseCount('departements', 15);

        $this->assertDatabaseHas('temperatures', [
            'temperature_moy' => 11.63,
            'temperature_min' => 7.77,
            'date_observation' => Carbon::createFromFormat('Y-m-d', '2019-10-22'),
            'temperature_max' => 15.48,
        ]);

        $this->assertDatabaseHas('temperatures', [
            'temperature_moy' => 12.25,
            'temperature_min' => 9.2,
            'date_observation' => Carbon::createFromFormat('Y-m-d', '2019-10-23'),
            'temperature_max' => 15.3,
        ]);
        $this->assertDatabaseHas('departements', [
            'nom' => 'Finistère',
            'code_insee' => 29,
        ]);

        $this->assertDatabaseHas('departements', [
            'nom' => 'Pas-de-Calais',
            'code_insee' => 62,
        ]);
    }
}
