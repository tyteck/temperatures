<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Services\TemperatureSelectionService;
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
        $this->markTestIncomplete('todo');
        $start = now()->subWeek();
        $end = now();
        $results = TemperatureSelectionService::period($start, $end)->get();
        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(0, $results);
    }
    // temp partially in range

    // temp by day

    // temp by week

    // temp by month

    // selecting some dep only
}
