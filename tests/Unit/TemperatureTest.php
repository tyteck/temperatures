<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Departement;
use App\Models\Temperature;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class TemperatureTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected Temperature $temperature;
    protected Departement $departement;

    public function setUp(): void
    {
        parent::setUp();
        $this->departement = Departement::factory()->create();
        $this->temperature = Temperature::factory()
            ->departement($this->departement)
            ->create()
        ;
    }

    /** @test */
    public function it_should_get_department_relation(): void
    {
        $this->assertNotNull($this->temperature->departement);
        $this->assertInstanceOf(Departement::class, $this->temperature->departement);
        $this->assertEqualsCanonicalizing(
            $this->departement->toArray(),
            $this->temperature->departement->toArray(),
        );
    }
}
