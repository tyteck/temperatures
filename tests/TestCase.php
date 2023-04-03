<?php

declare(strict_types=1);

namespace Tests;

use Database\Seeders\DepartementSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function seedDepartments(): void
    {
        $this->seed(DepartementSeeder::class);
    }
}
