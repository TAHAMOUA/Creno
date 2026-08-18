<h1>Mes rendez-vous</h1>

@foreach ($rendezVous as $rendezVousItem)
    <div>
        {{ $rendezVousItem->creneau->date }}
        {{ $rendezVousItem->statut }}
    </div>
@endforeach