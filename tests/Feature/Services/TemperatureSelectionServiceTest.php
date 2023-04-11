<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Enums\PeriodUnits;
use App\Models\Departement;
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

        $this->assertEquals(
            "CONCAT(YEAR(date_observation), '-', MONTH(date_observation)) as {$alias}",
            TemperatureSelectionService::period($start, $end)->setUnit(PeriodUnits::MONTH)->selectPeriod('date_observation', $alias)
        );

        $this->assertEquals(
            "CONCAT(YEAR(date_observation), '-', WEEK(date_observation)) as {$alias}",
            TemperatureSelectionService::period($start, $end)->setUnit(PeriodUnits::WEEK)->selectPeriod('date_observation', $alias)
        );

        $this->assertEquals(
            "YEAR(date_observation) as {$alias}",
            TemperatureSelectionService::period($start, $end)->setUnit(PeriodUnits::YEAR)->selectPeriod('date_observation', $alias)
        );

        $this->assertEquals(
            "date_observation as {$alias}",
            TemperatureSelectionService::period($start, $end)->setUnit(PeriodUnits::DAY)->selectPeriod('date_observation', $alias)
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
    public function it_should_return_two_days_selection(): void
    {
        $this->createSomeTemperatures(
            Carbon::createFromFormat('Y-m-d', '2018-01-05'),
            Carbon::createFromFormat('Y-m-d', '2018-01-10')
        );

        $start = Carbon::createFromFormat('Y-m-d', '2018-01-06');
        $end = Carbon::createFromFormat('Y-m-d', '2018-01-07');

        $results = TemperatureSelectionService::period($start, $end)->setUnit(PeriodUnits::DAY)->get();

        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(2, $results);
    }

    /** @test */
    public function it_should_return_one_month_selection(): void
    {
        $this->createSomeTemperatures(
            Carbon::createFromFormat('Y-m-d', '2018-01-05'),
            Carbon::createFromFormat('Y-m-d', '2018-01-10')
        );

        $start = Carbon::createFromFormat('Y-m-d', '2018-01-01');
        $end = Carbon::createFromFormat('Y-m-d', '2018-01-15');

        $results = TemperatureSelectionService::period($start, $end)->get();
        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_should_return_two_monthes_selection(): void
    {
        $this->createSomeTemperatures(
            Carbon::createFromFormat('Y-m-d', '2018-01-25'),
            Carbon::createFromFormat('Y-m-d', '2018-02-05')
        );

        $start = Carbon::createFromFormat('Y-m-d', '2018-01-01');
        $end = Carbon::createFromFormat('Y-m-d', '2018-02-28');

        $results = TemperatureSelectionService::period($start, $end)->get();
        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(2, $results);
    }

    /** @test */
    public function it_should_return_two_weeks_selection(): void
    {
        $this->createSomeTemperatures(
            Carbon::createFromFormat('Y-m-d', '2022-07-04'),
            Carbon::createFromFormat('Y-m-d', '2022-07-17')
        );

        $start = Carbon::createFromFormat('Y-m-d', '2022-07-06');
        $end = Carbon::createFromFormat('Y-m-d', '2022-07-13');

        $results = TemperatureSelectionService::period($start, $end)->setUnit(PeriodUnits::WEEK)->get();
        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(2, $results);
    }

    /** @test */
    public function it_should_return_two_monthes_for_single_department(): void
    {
        // create some temperatures
        $this->createSomeTemperatures(
            Carbon::createFromFormat('Y-m-d', '2018-01-25'),
            Carbon::createFromFormat('Y-m-d', '2018-02-05'),
        );

        // create temperatures for specific one
        $department = Departement::factory()->codeInsee('06')->create();
        $this->createSomeTemperatures(
            Carbon::createFromFormat('Y-m-d', '2018-01-25'),
            Carbon::createFromFormat('Y-m-d', '2018-02-05'),
            $department
        );

        $start = Carbon::createFromFormat('Y-m-d', '2018-01-01');
        $end = Carbon::createFromFormat('Y-m-d', '2018-02-28');

        $results = TemperatureSelectionService::period($start, $end)
            ->inDepartment($department->code_insee)
            ->get()
        ;

        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(2, $results);
    }

    /*
    |--------------------------------------------------------------------------
    | helpers & providers
    |--------------------------------------------------------------------------
    */
    protected function createSomeTemperatures(Carbon $start, Carbon $end, ?Departement $department = null): void
    {
        if ($department === null) {
            $department = Departement::factory()->create();
        }

        $start->startOfDay();
        while ($start->isBefore($end)) {
            Temperature::factory()
                ->departement($department)
                ->dateObservation($start)
                ->create()
            ;
            $start->addDay();
        }
    }
}
