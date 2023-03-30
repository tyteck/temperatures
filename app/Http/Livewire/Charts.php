<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Download;
use Carbon\Carbon;
use Livewire\Component;

class Charts extends Component
{
    public array $abscissa = [];
    public array $ordinate = [];

    public function mount(): void
    {
        $this->buildCoordinates();
    }

    public function selectingPeriod(int $index): void
    {
        $this->buildCoordinates();

        $this->emit('updateChartsData');
    }

    public function render()
    {
        return view('livewire.charts');
    }

    /**
     * @return array<Carbon>
     */
    public function fromPeriodToDates(?int $period = null): array
    {
        return match ($period) {
            self::PERIOD_LAST_WEEK => [
                now()->subWeek()->startOfWeek(weekStartsAt: Carbon::MONDAY), now()->subWeek()->endOfWeek(weekEndsAt: Carbon::SUNDAY),
            ],
            self::PERIOD_LAST_MONTH => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            self::PERIOD_THIS_WEEK => [now()->startOfWeek(weekStartsAt: Carbon::MONDAY), now()->endOfWeek(weekEndsAt: Carbon::SUNDAY)],
            self::PERIOD_THIS_MONTH => [now()->startOfMonth(), now()->endOfMonth()],
            self::PERIOD_LAST_QUARTER => [now()->startOfMonth()->subMonth(3), now()->endOfMonth()],
            // this month
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    public function buildCoordinates(): void
    {
        [$startDateToKeep, $endDate] = $this->fromPeriodToDates($this->selectedPeriod);

        // filling fake downloads with 0
        $startDate = clone $startDateToKeep;
        $paddedDownloads = [];
        while ($startDate->lessThan($endDate)) {
            $paddedDownloads[$startDate->toDateString()] = 0;
            $startDate->addDay();
        }

        // getting downloads
        $downloads = Download::downloadsByInterval(
            startDate: $startDateToKeep,
            endDate: $endDate,
            channel: $this->channel,
        )->pluck('counted', 'log_day')->toArray();

        // merging with padded
        $downloads = array_merge($paddedDownloads, $downloads);

        // building datasets
        $this->abscissa = $this->ordinate = [];
        foreach ($downloads as $dateKey => $counted) {
            $this->abscissa[] = $dateKey;
            $this->ordinate[] = $counted;
        }
    }
}
