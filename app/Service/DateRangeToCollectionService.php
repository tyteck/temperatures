<?php

declare(strict_types=1);

namespace App\Service;

use App\DataTransferObjects\DateRangeDTO;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DateRangeToCollectionService
{
    protected Collection $dates;

    private function __construct(protected Carbon $since, protected ?Carbon $to = null)
    {
        $this->dates = collect();

        throw_if($since->isAfter($to), new \InvalidArgumentException('Invalid parameter range. Parameter "since" should be before "to".'));
    }

    public static function range(...$params)
    {
        return new static(...$params);
    }

    /**
     * @return Collection<DateRangeDTO>
     */
    public function toMonthes(): Collection
    {
        $progress = clone $this->since;
        while ($progress->isBefore($this->to)) {
            $end = $progress->copy()->endOfMonth();
            if ($end->isAfter($this->to)) {
                $end = $this->to;
            }
            $this->dates->push(DateRangeDTO::toObject($progress->copy(), $end));
            $progress->addMonth()->startOfMonth();
        }

        return $this->dates;
    }
}
