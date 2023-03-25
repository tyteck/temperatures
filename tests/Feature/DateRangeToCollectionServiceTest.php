<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\DateRangeToCollectionService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class DateRangeToCollectionServiceTest extends TestCase
{
    /** @test */
    public function service_is_running_properly_for_three_monthes(): void
    {
        $since = Carbon::createFromFormat('Y-m-d', '2022-02-14');
        $to = Carbon::createFromFormat('Y-m-d', '2022-04-20');
        $results = DateRangeToCollectionService::range($since, $to)->toMonthes();

        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(3, $results);

        $this->assertEquals($since->toDateString(), $results->first()->start()->toDateString());
        $this->assertEquals($to->toDateString(), $results->last()->finish()->toDateString());
    }

    /** @test */
    public function service_is_running_properly_for_many_monthes(): void
    {
        $since = Carbon::createFromFormat('Y-m-d', '2018-09-17');
        $to = Carbon::createFromFormat('Y-m-d', '2021-11-05');
        $results = DateRangeToCollectionService::range($since, $to)->toMonthes();

        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(39, $results);

        $this->assertEquals($since->toDateString(), $results->first()->start()->toDateString());
        $this->assertEquals($to->toDateString(), $results->last()->finish()->toDateString());
    }

    /** @test */
    public function service_is_running_properly_for_one_day(): void
    {
        $expectedDate = '2022-02-14';
        $since = Carbon::createFromFormat('Y-m-d', $expectedDate);
        $to = clone $since;
        $results = DateRangeToCollectionService::range($since, $to)->toMonthes();

        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(1, $results);

        $this->assertEquals($since->toDateTimeString(), $expectedDate . ' 00:00:00');
        $this->assertEquals($to->toDateTimeString(), $expectedDate . ' 23:59:59');
    }

    /** @test */
    public function when_since_after_to_should_fail(): void
    {
        $since = now()->addMonth();
        $to = now();
        $this->expectException(\InvalidArgumentException::class);
        DateRangeToCollectionService::range($since, $to);
    }
}
