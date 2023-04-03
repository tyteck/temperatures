<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PeriodUnits;
use App\Models\Temperature;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TemperatureSelectionService
{
    public const DEFAULT_UNIT = PeriodUnits::MONTH;

    protected Collection $departements;

    private function __construct(
        protected Carbon $start,
        protected Carbon $end,
        protected PeriodUnits $unit = self::DEFAULT_UNIT
    ) {
        $this->departements = collect();
        $this->start->startOfDay();
        $this->end->endOfDay();
    }

    public static function period(Carbon $start, Carbon $end, PeriodUnits $unit = self::DEFAULT_UNIT)
    {
        return new static($start, $end, $unit);
    }

    public function get(): EloquentCollection
    {
        // default group by
        $periodAlias = 'period';
        $groupBy = [$periodAlias, 'departement_id'];

        $query = Temperature::query()
            ->with('departement')
            ->select(
                'departement_id',
                DB::raw('AVG(temperature_moy) as moyenne'),
                DB::raw($this->selectPeriod('date_observation', $periodAlias))
            )
            ->whereBetween('date_observation', [$this->start->toDateString(), $this->end->toDateString()])
        ;

        if ($this->departements->isNotEmpty()) {
            $query->whereIn('departement_id', $this->departements);
        }

        return $query
            ->groupBy($groupBy)
            ->get()
        ;
    }

    public function setUnit(PeriodUnits $periodUnit): static
    {
        $this->unit = $periodUnit;

        return $this;
    }

    public function selectPeriod(string $column, string $alias): string
    {
        $format = match ($this->unit) {
            PeriodUnits::YEAR => "YEAR({$column})",
            PeriodUnits::MONTH => "YEAR({$column}), '-', MONTH({$column})",
            default => "YEAR({$column}), '-', MONTH({$column})",
        };

        return "CONCAT({$format}) as {$alias}";
    }

    public function groupByPeriod(Collection $columns): string
    {
        return $columns->implode(', ');
    }
}
