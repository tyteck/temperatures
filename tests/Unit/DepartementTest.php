<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Departement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Sequence;
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
    public function by_code_insee_return_empty_collection_when_invalid_code_insee(): void
    {
        $result = Departement::byCodeInsee('invalid-code-insee');

        $this->assertNotNull($result);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEmpty($result);
    }

    /** @test */
    public function by_code_insee_return_one_item_collection_with_single_valid_code_as_string(): void
    {
        $departement = Departement::factory()
            ->create(['code_insee' => '06'])
        ;

        $results = Departement::byCodeInsee('06,83');
        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(1, $results);

        $this->assertEqualsCanonicalizing(
            $departement->toArray(),
            $results->sole()->toArray()
        );
    }

    /** @test */
    public function by_code_insee_return_valid_collection_with_valid_codes_as_string(): void
    {
        $departements = Departement::factory(2)
            ->state(new Sequence(
                ['code_insee' => '06'],
                ['code_insee' => '83'],
            ))
            ->create()
        ;
        // one more
        Departement::factory()->create(['code_insee' => '75']);

        $results = Departement::byCodeInsee('06,83');

        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(2, $results);

        $departements->each(function (Departement $departement) use ($results): void {
            $this->assertTrue($results->contains('code_insee', '=', $departement->code_insee));

            $this->assertEqualsCanonicalizing(
                $departement->toArray(),
                $results->where('code_insee', '=', $departement->code_insee)->sole()->toArray()
            );
        });
    }

    /** @test */
    public function by_code_insee_return_valid_collection_with_valid_codes_as_array(): void
    {
        $departements = Departement::factory(2)
            ->state(new Sequence(
                ['code_insee' => '06'],
                ['code_insee' => '83'],
            ))
            ->create()
        ;

        $results = Departement::byCodeInsee(['06', '83']);
        $this->assertNotNull($results);
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(2, $results);

        $departements->each(function (Departement $departement) use ($results): void {
            $this->assertTrue($results->contains('code_insee', '=', $departement->code_insee));

            dump(
                $departement->toArray(),
                $results->where('code_insee', '=', $departement->code_insee)->sole()->toArray()
            );
            $this->assertEqualsCanonicalizing(
                $departement->toArray(),
                $results->where('code_insee', '=', $departement->code_insee)
                    ->sole()
                    ->toArray()
            );
        });
    }
}
