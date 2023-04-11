<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PeriodUnits;
use App\Models\Temperature;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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

    public function get(): Collection
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
            ->whereBetween(
                'date_observation',
                [
                    $this->start->startOfDay()->toDateString(),
                    $this->end->endOfDay()->toDateString(),
                ]
            )
        ;

        if ($this->departements->isNotEmpty()) {
            $query->whereHas('departement', fn (Builder $query) => $query->whereIn('code_insee', $this->departements));
        }

        return $query
            ->groupBy($groupBy)
            ->get()
            ->mapWithKeys(fn (Temperature $temperature) => [$temperature->period => round($temperature->moyenne, 2)])
        ;
    }

    public function setUnit(PeriodUnits $periodUnit): static
    {
        $this->unit = $periodUnit;

        return $this;
    }

    public function inDepartment(string|array $departments): static
    {
        $departments = is_array($departments) ? $departments : func_get_args();

        array_map(fn (string $department) => $this->departements->push($department), $departments);

        return $this;
    }

    public function selectPeriod(string $column, string $alias): string
    {
        return match ($this->unit) {
            PeriodUnits::DAY => "{$column} as {$alias}",
            PeriodUnits::MONTH => "CONCAT(YEAR({$column}), '-', MONTH({$column})) as {$alias}",
            PeriodUnits::WEEK => "CONCAT(YEAR({$column}), '-', WEEK({$column})) as {$alias}",
            PeriodUnits::YEAR => "YEAR({$column}) as {$alias}",
            default => "CONCAT(YEAR({$column}), '-', MONTH({$column})) as {$alias}",
        };
    }

    public function groupByPeriod(Collection $columns): string
    {
        return $columns->implode(', ');
    }
}
