<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Creneau extends Model
{
    protected $fillable = [
        'date',
        'heure_debut',
        'duree',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'heure_debut' => 'datetime:H:i',
            'duree' => 'integer',
        ];
    }
    public function scopePasses(Builder $query): Builder
{
    return $query->whereRaw(
        "TIMESTAMP(date, heure_debut) < ?",
        [Carbon::now()]
    );
}
}
