<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @property Carbon $start
 * @property Carbon $finish
 */
class DateRangeDTO implements Arrayable
{
    private function __construct(public Carbon $start, public Carbon $finish)
    {
        $this->start->startOfDay();
        $this->finish->endOfDay();
        // dump('received', $this->start->toDateTimeString(), $this->finish->toDateTimeString());
    }

    public static function toObject(...$params)
    {
        return new static(...$params);
    }

    /**
     * Adding one second on the nb_days column to get realistic numbre of nb_days.
     * else {
     * # code...
     * }.
     */
    public function toArray(): array
    {
        return [
            'start' => $this->start->toDateString(),
            'finish' => $this->finish->toDateString(),
            'nb_days' => $this->finish->copy()->addSecond()->diffInDays($this->start),
        ];
    }

    public function start(): Carbon
    {
        return $this->start;
    }

    public function finish(): Carbon
    {
        return $this->finish;
    }
}
