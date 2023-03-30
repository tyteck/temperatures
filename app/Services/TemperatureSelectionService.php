<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PeriodUnits;
use App\Models\Temperature;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class TemperatureSelectionService
{
    public const DEFAULT_UNIT = PeriodUnits::DAY;

    protected Collection $dates;

    private function __construct(protected Carbon $start, protected Carbon $end, protected PeriodUnits $unit = self::DEFAULT_UNIT)
    {
    }

    public static function period(Carbon $start, Carbon $end, PeriodUnits $unit = self::DEFAULT_UNIT)
    {
        return new static($start, $end, $unit);
    }

    public function get(): EloquentCollection
    {
        $query = Temperature::query();

        $query->whereBetween('date_observation', [$this->start, $this->end]);

        return $query->get();
    }
}
