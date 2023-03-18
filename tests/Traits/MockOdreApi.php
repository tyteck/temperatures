<?php

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Support\Facades\Http;
use Tests\Enums\FixtureFile;

trait MockOdreApi
{
    protected function fakeSingleDataset(): void
    {
        $this->fakeOdreWithFixture(FixtureFile::SINGLE_DATASET);
    }

    protected function fakeSmallDataset(): void
    {
        $this->fakeOdreWithFixture(FixtureFile::SMALL_DATASET);
    }

    protected function fakeLargeDataset(): void
    {
        $this->fakeOdreWithFixture(FixtureFile::LARGE_DATASET);
    }

    protected function fakeOdreWithFixture(FixtureFile $fixtureFile): void
    {
        Http::fake([
            'https://odre.opendatasoft.com/api/records/1.0/search/*' => Http::response(
                file_get_contents($fixtureFile->path()),
                200
            ),
        ]);
    }
}
