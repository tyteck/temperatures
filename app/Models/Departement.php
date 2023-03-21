<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property string $code_insee
 * @property string $nom
 */
class Departement extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    public function enregistrements(): HasMany
    {
        return $this->hasMany(Temperature::class);
    }

    public static function byCodeInsee($inseeCodes): Collection
    {
        if (!is_array($inseeCodes)) {
            $inseeCodes = Str::of($inseeCodes)->explode(',');
        }

        return self::whereIn('code_insee', $inseeCodes)->get();
    }
}
