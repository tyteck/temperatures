<?php

declare(strict_types=1);

namespace App\Services\V1;

use App\Enums\QueryOperators;

class TemperatureQuery
{
    protected $allowedParams = [
        'department' => ['eq'],
        'date' => ['eq'],
    ];

    protected $columnMap = [
        'department' => 'department_id',
        'date' => 'date_observation',
    ];

    private function __construct(protected array $queryParams)
    {
    }

    public static function from(array $queryParams): static
    {
        return new static($queryParams);
    }

    /**
     * This will filter query string parameters and transform them to a where query for eloquent
     * in browser        ?foo[eq]=bar&department[eq]=06&date[eq]=2021-13-12
     * request->query()  ['foo' => [ 'eq' => 'bar' ], 'department' => [ 'eq' => '06 ], 'date' => ['eq' => '2021-13-12'] ]
     * eloquent          [[ 'departement_id', '=', '06' ], ['date_observation', '=', '2021-13-12']].
     *
     * @todo would love to make it a oneliner
     * return collect(...)->toArray(); but I want array of array
     */
    public function transform(): array
    {
        $result = collect();

        collect($this->queryParams)
            ->only(array_keys($this->allowedParams)) // keeping only allowed params
            ->each(
                fn ($queryParam, $column) => $result->push(
                    collect($queryParam)
                        ->only($this->allowedParams[$column]) // keeping only allowed operators
                        ->mapWithKeys(
                            fn ($value, $operator) => [
                                $this->columnMap[$column],
                                QueryOperators::from($operator)->value(),
                                $value,
                            ]
                        )
                )
            )
        ;

        return $result->toArray();
    }
}
