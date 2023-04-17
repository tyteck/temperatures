<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\PeriodUnits;
use App\Services\TemperatureSelectionService;
use Carbon\Carbon;
use Livewire\Component;

class Charts extends Component
{
    public array $abscissa = [];
    public array $ordinate = [];
    public Carbon $currentDate;
    public int $selectedPeriod = self::PERIOD_ALL;
    public PeriodUnits $selectedUnit = PeriodUnits::MONTH;
    public string $selectedPeriodLabel;
    public array $periods = [];

    public const PERIOD_ALL = 0;
    public const PERIOD_THIS_YEAR = 1;
    public const PERIOD_LAST_YEAR = 2;

    public function mount(): void
    {
        $this->periods = [
            self::PERIOD_ALL => 'depuis janvier 2018',
            self::PERIOD_THIS_YEAR => 'cette année',
            self::PERIOD_LAST_YEAR => "l'année dernière",
        ];

        $this->selectedPeriodLabel = $this->periods[$this->selectedPeriod];
        $this->buildCoordinates();
    }

    public function selectingPeriod(int $index): void
    {
        $this->selectedPeriod = $index;
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
    public function fromPeriodToDates(): array
    {
        return match ($this->selectedPeriod) {
            self::PERIOD_LAST_YEAR => [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()],
            self::PERIOD_THIS_YEAR => [now()->startOfYear(), now()->subMonth()->endOfMonth()],
            // all
            self::PERIOD_ALL => [Carbon::createFromFormat('Y-m-d', '2018-01-01')->startOfDay(), now()->subMonth()->endOfMonth()],
            default => [Carbon::createFromFormat('Y-m-d', '2018-01-01')->startOfDay(), now()->subMonth()->endOfMonth()],
        };
    }

    public function buildCoordinates(): void
    {
        [$startDateToKeep, $endDate] = $this->fromPeriodToDates();

        // filling fake downloads with 0
        $this->currentDate = clone $startDateToKeep;
        $padded = [];
        while ($this->currentDate->lessThan($endDate)) {
            $padded[$this->currentDate->format('Y-n')] = 0;
            $this->incrementPeriod();
        }

        // getting downloads
        $temperatures = TemperatureSelectionService::period($startDateToKeep, $endDate)
            ->get()
            ->toArray()
        ;

        // merging with padded
        $temperatures = array_merge($padded, $temperatures);

        // building datasets
        $this->abscissa = $this->ordinate = [];
        foreach ($temperatures as $dateKey => $counted) {
            $this->abscissa[] = Carbon::createFromFormat('Y-n', $dateKey)->translatedFormat('M Y');
            $this->ordinate[] = $counted;
        }
    }

    protected function incrementPeriod(): void
    {
        match ($this->selectedUnit) {
            PeriodUnits::YEAR => $this->currentDate->addYear(),
            PeriodUnits::MONTH => $this->currentDate->addMonth(),
            default => $this->currentDate->addMonth(),
        };
    }
}
