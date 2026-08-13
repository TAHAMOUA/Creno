<?php

namespace App\Models;

use App\Models\RendezVous;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class Creneau extends Model
{
    protected $table = 'creneaux';

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
    if ($query->getConnection()->getDriverName() === 'sqlite') {
        return $query->whereRaw(
            "datetime(date(date) || ' ' || time(heure_debut)) < ?",
            [now()->format('Y-m-d H:i:s')]
        );
    }

    return $query->whereRaw(
        "TIMESTAMP(DATE(date), TIME(heure_debut)) < ?",
        [now()->format('Y-m-d H:i:s')]
    );
}

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class);
    }

  public function scopeDisponibles(Builder $query): Builder
{
    if ($query->getConnection()->getDriverName() === 'sqlite') {
        return $query
            ->whereRaw(
                "datetime(date(date) || ' ' || time(heure_debut)) >= ?",
                [now()->format('Y-m-d H:i:s')]
            )
            ->whereDoesntHave('rendezVous', function ($query) {
                $query->whereIn('statut', [
                    'en_attente',
                    'confirme',
                ]);
            });
    }

    return $query
        ->whereRaw(
            "TIMESTAMP(DATE(date), TIME(heure_debut)) >= ?",
            [now()->format('Y-m-d H:i:s')]
        )
        ->whereDoesntHave('rendezVous', function ($query) {
            $query->whereIn('statut', [
                'en_attente',
                'confirme',
            ]);
        });
}

    public function chevauche(Creneau $autre): bool
{
    $dateA = Carbon::parse($this->getAttribute('date'))->format('Y-m-d');
    $dateB = Carbon::parse($autre->getAttribute('date'))->format('Y-m-d');

    if ($dateA !== $dateB) {
        return false;
    }

    $heureA = Carbon::parse($this->getAttribute('heure_debut'))->format('H:i:s');
    $heureB = Carbon::parse($autre->getAttribute('heure_debut'))->format('H:i:s');

    $debutA = Carbon::parse($dateA . ' ' . $heureA);
    $debutB = Carbon::parse($dateB . ' ' . $heureB);

    $finA = $debutA->copy()->addMinutes((int) $this->duree);
    $finB = $debutB->copy()->addMinutes((int) $autre->duree);

    return $debutA < $finB && $debutB < $finA;
}
}