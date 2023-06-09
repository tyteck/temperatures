<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Enums\SortWay;
use App\Models\Departement;
use App\Services\OdreQueryBuilderService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class OdreQueryBuilderServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @test */
    public function query_should_be_correct_with_date_range(): void
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

    /** @test */
    public function query_should_be_correct_with_single_date(): void
    {
        $expected = 'https://odre.opendatasoft.com/api/records/1.0/search/?dataset=temperature-quotidienne-departementale&rows=31&facet=date_obs&facet=departement&q=code_insee_departement%3A06&q=date_obs%3E%3D%222022-12-01%22&timezone=Europe%2FParis&sort=-date_obs';

        $dataset = 'temperature-quotidienne-departementale';
        $result = OdreQueryBuilderService::create($dataset, 31)
            ->addFacet('date_obs', 'departement')
            ->addQuery('code_insee_departement', '06')
            ->forPeriod('date_obs', Carbon::create('2022-12-01'))
            ->timezone(config('temperatures.timezone'))
            ->sortedBy('date_obs', SortWay::ASC)
            ->get()
        ;

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function query_should_be_correct_adding_department(): void
    {
        $departement = Departement::factory()->name('Alpes-Maritimes')->create();
        $expected = 'https://odre.opendatasoft.com/api/records/1.0/search/?dataset=temperature-quotidienne-departementale&rows=31&facet=date_obs&facet=departement&refine.departement=Alpes-Maritimes&timezone=Europe%2FParis';

        $dataset = 'temperature-quotidienne-departementale';
        $result = OdreQueryBuilderService::create($dataset, 31)
            ->addFacet('date_obs', 'departement')
            ->addDepartment($departement)
            ->timezone(config('temperatures.timezone'))
            ->get()
        ;

        $this->assertEquals($expected, $result);
    }
}
