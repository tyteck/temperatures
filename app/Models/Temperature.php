<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Carbon      $date_observation
 * @property Departement $departement
 * @property float       $temperature_moy
 * @property float       $temperature_min
 * @property float       $temperature_max
 * @property int         $timestamp
 */
class Temperature extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    protected $dates = [
        'date_observation',
    ];

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }
}
