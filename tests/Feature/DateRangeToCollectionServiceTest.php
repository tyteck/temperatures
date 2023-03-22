<?php

declare(strict_types=1);

namespace Tests\Feature;

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
    public function service_is_running_properly(): void
    {
        $since = Carbon::create('first day of january 2022');
        $to = Carbon::create('last day of december 2022');
        $result = DateRangeToCollectionService::from($since, $to)->toCollection();

        $this->assertNotNull($result);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(12, $result);
    }
}
