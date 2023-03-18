<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Departement;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class DepartementTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @test */
    public function by_code_insee_is_null_when_invalid_code_insee(): void
    {
        $this->assertNull(Departement::byCodeInsee('invalid-code-insee'));
    }

    /** @test */
    public function by_code_insee_is_ok_with_valid_code_insee(): void
    {
        $departement = Departement::factory()->create(['code_insee' => '06']);
        $result = Departement::byCodeInsee('06');
        $this->assertNotNull($result);
        $this->assertInstanceOf(Departement::class, $result);
        $this->assertEqualsCanonicalizing($departement->toArray(), $result->toArray());
    }
}
