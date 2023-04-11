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
    public int $selectedPeriod = 0;
    public PeriodUnits $selectedUnit = PeriodUnits::MONTH;
    public string $selectedPeriodLabel;
    public array $periods = [];

    public const PERIOD_ALL = 0;

    public function mount(): void
    {
        $this->periods = [
            self::PERIOD_ALL => 'Tout',
        ];

        $this->selectedPeriodLabel = $this->periods[$this->selectedPeriod];
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
            // all
            self::PERIOD_ALL => [Carbon::createFromFormat('Y-m-d', '2018-01-01')->startOfDay(), now()],
            default => [Carbon::createFromFormat('Y-m-d', '2018-01-01')->startOfDay(), now()],
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
            $this->abscissa[] = $dateKey;
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
