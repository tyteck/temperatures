<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TemperatureCollection;
use App\Http\Resources\V1\TemperatureResource;
use App\Models\Temperature;

class TemperatureController extends Controller
{
    public function index()
    {
        return new TemperatureCollection(Temperature::query()->take(10)->get());
    }

    public function show(Temperature $temperature)
    {
        return new TemperatureResource($temperature);
    }
}
