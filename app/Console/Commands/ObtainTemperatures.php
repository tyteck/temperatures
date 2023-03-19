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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ObtainTemperatures extends Command
{
    public const PERIOD_FORMAT = 'Y-m-d';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temperatures:get 
                            {--since= : getting temperatures since date specified with format Y-m-d.} 
                            {--to= : getting temperatures to date specified with format Y-m-d.}
                            {--departments= : getting temperatures for specified departement(s) (by insee code, separated with comma).}'
    ;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'process temperatures from open data';

    protected Carbon $since;
    protected Carbon $to;
    protected Collection $departments;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            // since
            $this->since = Carbon::createFromFormat(self::PERIOD_FORMAT, $this->option('since'));
            throw_if(
                $this->since->isBefore(Carbon::createMidnightDate(2018, 1, 1))
                || $this->since->isAfter(now()->startOfDay()->subMonth()->endOfMonth()),
                new OdreApiDateIsNotAvailableException('ODRE api is only available from January 2018 to the end of previous month.')
            );

            // end period
            $this->to = $this->option('to') ?
                Carbon::createFromFormat(self::PERIOD_FORMAT, $this->option('to')) :
                $this->since->copy()->addMonth()
            ;

            // departements
            $departments = $this->option('departments') ? Str::of($this->option('departments'))->explode(',') : Departement::all();

            $departments->each(fn (Departement $departement) => $this->processDataset($departement));
        } catch (\Throwable $thrown) {
            Log::error($thrown->getMessage());
            $this->error($thrown->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function processDataset(Departement $departement): void
    {
        // build query
        $odreQuery = OdreQueryBuilderService::create(config('temperatures.dataset'), 31)
            ->addFacet('date_obs', 'departement')
            ->addDepartment($departement)
            ->forPeriod('date_obs', $this->since, $this->to)
            ->timezone(config('temperatures.timezone'))
            ->sortedBy('date_obs', SortWay::ASC)
            ->get()
        ;

        // get contents
        $json = Http::get($odreQuery)->body();

        // process json
        ProcessDatasetService::from($json)->store();
    }
}
