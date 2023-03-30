<?php

declare(strict_types=1);

namespace Tests\Feature\Services\V1;

use App\Services\V1\TemperatureQuery;
use Tests\TestCase;

/**
 * @internal
 */
class TemperatureQueryTest extends TestCase
{
    /** @test */
    public function it_should_return_empty_array_with_invalid_params(): void
    {
        // $request->query() is returning one array
        $foolishParams = ['foo' => [
            'eq' => 'bar',
        ]];
        $result = TemperatureQuery::from($foolishParams)->transform();
        $this->assertNotNull($result);
        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    /** @test */
    public function it_should_return_expected_array_with_some_valid_params(): void
    {
        $params = [
            'foo' => ['eq' => 'bar'],
            'department' => ['eq' => '06'],
            'date' => ['eq' => '2021-08-06'],
        ];
        $result = TemperatureQuery::from($params)->transform();
        $this->assertNotNull($result);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEqualsCanonicalizing([
            ['department_id', '=', '06'],
            ['date_observation', '=', '2021-08-06'],
        ], $result);
    }
}
