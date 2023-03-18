<?php

declare(strict_types=1);

namespace Tests\Enums;

enum FixtureFile: string
{
    case SINGLE_DATASET = 'single_dataset.json';

    case SMALL_DATASET = 'small_dataset.json';

    case LARGE_DATASET = 'large_dataset.json';

    public function path(): string
    {
        return fixtures_path($this->value);
    }
}
