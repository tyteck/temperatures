<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
