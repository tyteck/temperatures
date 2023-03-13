<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\SortWay;
use App\Models\Departement;
use App\Service\OdreQueryBuilderService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ObtainTemperatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temperatures:get';

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
        // start period
        // end period

        // get departements
        $departements = Departement::query()->orderBy('code_insee')->get();

        // build query
        $odreQuery = OdreQueryBuilderService::create(config('temperatures.dataset'), 31)
            ->addFacet('date_obs', 'departement')
            ->addQuery('code_insee_departement', '06')
            ->forPeriod('date_obs', Carbon::create('2022-12-01'), Carbon::create('2023-02-01'))
            ->timezone(config('temperatures.timezone'))
            ->sortedBy('date_obs', SortWay::ASC)
            ->get()
        ;

        // file get contents
        $json = file_get_contents($odreQuery);

        // process json

        return Command::SUCCESS;
    }
}
