<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('temperatures', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('departement_id')->constrained();
            $table->float('temperature_moy', 5, 2);
            $table->float('temperature_min', 5, 2);
            $table->float('temperature_max', 5, 2);
            $table->date('date_observation');
            $table->unique(['departement_id', 'date_observation']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temperatures');
    }
};
