<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SortWay;
use App\Models\Departement;
use Carbon\Carbon;

class OdreQueryBuilderService
{
    protected string $baseUrl;
    protected string $url = '';
    protected array $params = [];

    private function __construct(public string $dataset, public int $nbRows = 10)
    {
        $this->baseUrl = config('temperatures.base_url');
        $this->params[] = "dataset={$this->dataset}";
        $this->params[] = "rows={$this->nbRows}";
    }

    public static function create(...$params)
    {
        return new static(...$params);
    }

    public function addFacet(string|array $facets): static
    {
        if (!is_array($facets)) {
            $facets = func_get_args();
        }

        array_map(function ($facet): void {
            $this->params[] = "facet={$facet}";
        }, $facets);

        return $this;
    }

    public function addQuery(string $column, string $value): static
    {
        $this->params[] = 'q=' . urlencode("{$column}:{$value}");

        return $this;
    }

    public function forPeriod(string $column, Carbon $from, Carbon $to = null): static
    {
        $periodQuery = "q={$column}";
        if ($to === null) {
            $periodQuery .= urlencode('>="' . $from->format('Y-m-d') . '"');
        } else {
            $periodQuery .= urlencode(':[' . $from->format('Y-m-d')) .
                '+TO+' .
                urlencode($to->format('Y-m-d') . ']')
            ;
        }
        $this->params[] = $periodQuery;

        return $this;
    }

    public function sortedBy(string $sortedColumn, SortWay $sortWay): static
    {
        $prefix = $sortWay === SortWay::ASC ? '-' : '';

        $this->params[] = "sort={$prefix}{$sortedColumn}";

        return $this;
    }

    public function timezone(string $timezone): static
    {
        $this->params[] = 'timezone=' . urlencode($timezone);

        return $this;
    }

    public function addDepartment(Departement $departement): static
    {
        $this->params[] = "refine.departement={$departement->nom}";

        return $this;
    }

    public function get(): string
    {
        return $this->baseUrl . implode('&', $this->params);
    }
}
