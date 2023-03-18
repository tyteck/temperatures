<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\SortWay;
use App\Service\OdreQueryBuilderService;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class OdreQueryBuilderServiceTest extends TestCase
{
    /** @test */
    public function query_should_be_correct(): void
    {
        $expected = 'https://odre.opendatasoft.com/api/records/1.0/search/?dataset=temperature-quotidienne-departementale&rows=31&facet=date_obs&facet=departement&q=code_insee_departement%3A06&q=date_obs%3A%5B2022-12-01+TO+2023-02-01%5D&timezone=Europe%2FParis&sort=-date_obs';

        $dataset = 'temperature-quotidienne-departementale';
        $result = OdreQueryBuilderService::create($dataset, 31)
            ->addFacet('date_obs', 'departement')
            ->addQuery('code_insee_departement', '06')
            ->forPeriod('date_obs', Carbon::create('2022-12-01'), Carbon::create('2023-02-01'))
            ->timezone(config('temperatures.timezone'))
            ->sortedBy('date_obs', SortWay::ASC)
            ->get()
        ;

        $this->assertEquals($expected, $result);
    }
}
