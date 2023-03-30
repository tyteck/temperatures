<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TemperatureCollection;
use App\Http\Resources\V1\TemperatureResource;
use App\Models\Temperature;
use App\Services\V1\TemperatureQuery;
use Illuminate\Http\Request;

class TemperatureController extends Controller
{
    public function index(Request $request)
    {
        $eloquentWhere = TemperatureQuery::from($request->query())->transform();

        return new TemperatureCollection(
            Temperature::query()
                ->where($eloquentWhere)
                ->take(10)
                ->get()
        );
    }

    public function show(Temperature $temperature)
    {
        return new TemperatureResource($temperature);
    }
}
