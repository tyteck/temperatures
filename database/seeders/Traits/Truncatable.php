<?php

declare(strict_types=1);

namespace Database\Seeders\Traits;

use Illuminate\Support\Facades\DB;

trait Truncatable
{
    protected function disableForeignKeyCheck(): void
    {
        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
    }

    protected function enableForeignKeyCheck(): void
    {
        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    protected function truncateTable(string $tableName): void
    {
        $this->disableForeignKeyCheck();
        DB::table($tableName)->truncate();
        $this->enableForeignKeyCheck();
    }
}
