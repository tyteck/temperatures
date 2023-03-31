<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Enums\PeriodUnits;
use App\Models\Temperature;
use App\Services\TemperatureSelectionService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class TemperatureSelectionServiceTest extends TestCase
{
    use LazilyRefreshDatabase;
    protected TemperatureSelectionServiceTest $service;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_should_return_right_concat_syntax(): void
    {
        $start = now()->subWeek();
        $end = now();
        $alias = 'period';

        // testing default is sqlite
        $expected = "STRFTIME('%Y-%m', date_observation) AS period";
        $this->assertEquals(
            $expected,
            TemperatureSelectionService::period($start, $end)->setUnit(PeriodUnits::MONTH)->selectPeriod('date_observation', $alias)
        );

        // forcing mysql
        config(['database.default' => 'mysql']);
        $expected = "CONCAT(YEAR(date_observation), '-', MONTH(date_observation)) as period";
        $this->assertEquals(
            $expected,
            TemperatureSelectionService::period($start, $end)->setUnit(PeriodUnits::MONTH)->selectPeriod('date_observation', $alias)
        );
    }

    /** @test */
    public function it_should_return_empty_selection_when_none(): void
    {
        // no temp => no selection
        $start = now()->subWeek();
        $end = now();
        $results = TemperatureSelectionService::period($start, $end)->get();

        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(0, $results);
    }

    /** @test */
    public function it_should_return_empty_selection_when_out_of_range(): void
    {
        // creating 5 records in temperatures (5-10 january 2018)
        $this->createSomeTemperatures(
            Carbon::createFromFormat('Y-m-d', '2018-01-05'),
            Carbon::createFromFormat('Y-m-d', '2018-01-10')
        );

        $start = now()->subWeek();
        $end = now();

        $results = TemperatureSelectionService::period($start, $end)->get();
        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(0, $results);
    }

    /** @test */
    public function it_should_return_partial_selection_when_partially_in_range(): void
    {
        // creating 5 records in temperatures (5-10 january 2018)
        $this->createSomeTemperatures(
            Carbon::createFromFormat('Y-m-d', '2018-01-05'),
            Carbon::createFromFormat('Y-m-d', '2018-01-10')
        );

        // wanting 8-15 january 2018
        $start = Carbon::createFromFormat('Y-m-d', '2018-01-08');
        $end = Carbon::createFromFormat('Y-m-d', '2018-01-15');

        dump(Temperature::all()->toArray());
        // should have 2 days

        ray()->showQueries();
        $results = TemperatureSelectionService::period($start, $end)->get();
        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(2, $results);
    }
    // temp partially in range

    // temp by day

    // temp by week

    // temp by month

    // selecting some dep only

    /*
    |--------------------------------------------------------------------------
    | helpers & providers
    |--------------------------------------------------------------------------
    */
    protected function createSomeTemperatures(Carbon $start, Carbon $end): void
    {
        $start->startOfDay();
        while ($start->isBefore($end)) {
            Temperature::factory()
                ->dateObservation($start)
                ->create()
            ;
            $start->addDay();
        }
    }
}
