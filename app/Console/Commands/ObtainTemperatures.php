<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\SortWay;
use App\Exceptions\OdreApiDateIsNotAvailableException;
use App\Models\Departement;
use App\Service\OdreQueryBuilderService;
use App\Service\ProcessDatasetService;
use Carbon\Carbon;
use Illuminate\Console\Command;
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
                            {to? : getting temperatures to date specified with format Y-m-d.}
                            {department? : specific departement or all}'
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
            $since = Carbon::createFromFormat(self::PERIOD_FORMAT, $this->argument('since'))->startOfMonth()->startOfDay();
            throw_if(
                $since->isBefore(Carbon::createMidnightDate(2018, 1, 1)) || $since->isAfter(now()->startOfDay()),
                new OdreApiDateIsNotAvailableException('ODRE api is only available from January 2018 to yesterday.')
            );

            // end period
            $to = $this->argument('to') ?
                Carbon::createFromFormat(self::PERIOD_FORMAT, $this->argument('to'))->startOfMonth()->startOfDay() :
                $since->copy()->addMonth()
            ;

            // get departements
            $query = Departement::query();
            if ($this->argument('department')) {
                $query->where('code_insee', $this->argument('department'));
            }
            $departments = $query->get();
            dd($departments);
            // build query
            $odreQuery = OdreQueryBuilderService::create(config('temperatures.dataset'), 31)
                ->addFacet('date_obs', 'departement')
                ->addQuery('code_insee_departement', '06')
                ->forPeriod('date_obs', $since, $to)
                ->timezone(config('temperatures.timezone'))
                ->sortedBy('date_obs', SortWay::ASC)
                ->get()
            ;

            // file get contents
            $json = file_get_contents($odreQuery);

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
