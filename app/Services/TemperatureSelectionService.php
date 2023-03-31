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
    }

    public static function period(Carbon $start, Carbon $end, PeriodUnits $unit = self::DEFAULT_UNIT)
    {
        return new static($start, $end, $unit);
    }

    public function get(): EloquentCollection
    {
        // default group by
        $periodAlias = 'period';
        $groupBy = collect([$periodAlias]);

        $query = Temperature::query()
            ->with('departement')
            ->select(
                'departement_id',
                DB::raw('AVG(temperature_moy) as moyenne'),
                DB::raw($this->selectPeriod('date_observation', $periodAlias))
            )
        ;

        $query
            ->whereBetween('date_observation', [$this->start, $this->end])
        ;
        if (!empty($this->departements)) {
            $query->whereIn('departement_id', $this->departements);
            $groupBy[] = 'departement_id';
        }

        return $query->groupBy($this->groupByPeriod($groupBy))
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
        if (config('database.default') === 'sqlite') {
            $format = match ($this->unit) {
                PeriodUnits::YEAR => '%Y',
                PeriodUnits::MONTH => '%Y-%m',
                default => '%Y-%m',
            };

            return "STRFTIME('{$format}', {$column}) AS {$alias}";
        }

        $format = match ($this->unit) {
            PeriodUnits::YEAR => '%Y',
            PeriodUnits::MONTH => "YEAR({$column}), '-', MONTH({$column})",
            default => "YEAR({$column}), '-', MONTH({$column})",
        };

        return "CONCAT({$format}) as {$alias}";
    }

    public function groupByPeriod(Collection $columns): string
    {
        /* if (config('database.default') === 'sqlite') {
            $format = match ($this->unit) {
                PeriodUnits::YEAR => '%Y',
                PeriodUnits::MONTH => '%Y-%m',
                default => '%Y-%m',
            };

            return "STRFTIME('{$format}', {$columns->first()})";
        } */

        return $columns->implode(', ');
    }
}
