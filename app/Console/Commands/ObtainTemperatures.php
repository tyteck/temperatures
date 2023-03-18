<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\SortWay;
use App\Exceptions\OdreApiDateIsNotAvailableException;
use App\Service\OdreQueryBuilderService;
use App\Service\ProcessDatasetService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ObtainTemperatures extends Command
{
    public const PERIOD_FORMAT = 'Y-m-d';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temperatures:get 
                            {since : getting temperatures since date specified with format Y-m-d.} 
                            {to? : getting temperatures to date specified with format Y-m-d.}'
    ;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'process temperatures from open data';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            // since
            $since = Carbon::createFromFormat(self::PERIOD_FORMAT, $this->argument('since'));
            throw_if(
                $since->isBefore(Carbon::createMidnightDate(2018, 1, 1))
                || $since->isAfter(now()->startOfDay()->subMonth()->endOfMonth()),
                new OdreApiDateIsNotAvailableException('ODRE api is only available from January 2018 to the end of previous month.')
            );

            // end period
            $to = $this->argument('to') ?
                Carbon::createFromFormat(self::PERIOD_FORMAT, $this->argument('to')) :
                $since->copy()->addMonth()
            ;

            // build query
            $odreQuery = OdreQueryBuilderService::create(config('temperatures.dataset'), 31)
                ->addFacet('date_obs', 'departement')
                ->forPeriod('date_obs', $since, $to)
                ->timezone(config('temperatures.timezone'))
                ->sortedBy('date_obs', SortWay::ASC)
                ->get()
            ;

            // file get contents
            $json = Http::get($odreQuery)->body();

            // process json
            ProcessDatasetService::from($json)->store();
        } catch (\Throwable $thrown) {
            Log::error($thrown->getMessage());
            $this->error($thrown->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
