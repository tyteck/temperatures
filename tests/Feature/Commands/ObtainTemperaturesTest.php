<?php

declare(strict_types=1);

namespace Tests\Feature\Commands;

use App\Console\Commands\ObtainTemperatures;
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
            ->expectsOutputToContain('ODRE api is only available from January 2018 to yesterday.')
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
            ->expectsOutputToContain('ODRE api is only available from January 2018 to yesterday.')
        ;
    }
}
