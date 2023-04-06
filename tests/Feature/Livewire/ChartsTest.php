<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Http\Livewire\Charts;
use App\Models\Download;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ChartsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->markTestIncomplete('to be done.');
    }

    /** @test */
    public function basic_checking_with_default(): void
    {
        Livewire::test(Charts::class)
            ->assertSet('selectedPeriod', 0)
            ->assertCount('abscissa', date('t'))
            ->assertCount('ordinate', date('t'))
        ;
    }

    /** @test */
    public function basic_checking_with_last_week(): void
    {
        $this->addDownloadsForMediaDuringPeriod($this->media, now()->subDays(30), now(), 20);

        Livewire::test(Charts::class, ['channel' => $this->channel])
            ->call('selectingPeriod', Charts::PERIOD_LAST_WEEK)
            ->assertCount('abscissa', 7)
            ->assertCount('ordinate', 7)
        ;
    }

    /*
    |--------------------------------------------------------------------------
    | helpers & providers
    |--------------------------------------------------------------------------
    */

    /**
     * @return int nb of counted downloads
     */
    public function addDownloadsForMediaDuringPeriod(Media $media, Carbon $startDate, Carbon $endDate, int $chancesOfGettingOneDownload = 100): int
    {
        $countedDownloads = 0;
        while ($startDate->lessThan($endDate)) {
            if (fake()->boolean($chancesOfGettingOneDownload)) {
                $download = Download::factory()->media($media)->logDate($startDate)->create();
                $countedDownloads += $download->counted;
            }
            $startDate->addDay();
        }

        return $countedDownloads;
    }
}
