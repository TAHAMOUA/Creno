<h1>Gestion des créneaux</h1>

@if (session('success'))
    <p>{{ session('success') }}</p>
@endif

@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif


{{-- Formulaire d'ajout --}}
<h2>Ajouter un créneau</h2>

<form method="POST" action="{{ route('admin.creneaux.store') }}">
    @csrf

    <div>
        <label for="date">Date</label>
        <input
            type="date"
            id="date"
            name="date"
            value="{{ old('date') }}"
        >
    </div>

    <div>
        <label for="heure_debut">Heure de début</label>
        <input
            type="time"
            id="heure_debut"
            name="heure_debut"
            value="{{ old('heure_debut') }}"
        >
    </div>

    <div>
        <label for="duree">Durée (minutes)</label>
        <input
            type="number"
            id="duree"
            name="duree"
            value="{{ old('duree') }}"
            min="1"
        >
    </div>

    <button type="submit">
        Ajouter le créneau
    </button>
</form>


{{-- Liste des créneaux --}}
<h2>Créneaux existants</h2>

@forelse ($creneaux as $creneau)

    <div>
        <p>
            Date :
            {{ $creneau->date->format('d/m/Y') }}
        </p>

        <p>
            Heure :
            {{ $creneau->heure_debut->format('H:i') }}
        </p>

        <p>
            Durée :
            {{ $creneau->duree }} minutes
        </p>


        {{-- Formulaire de modification --}}
        <h3>Modifier le créneau</h3>

        <form
            method="POST"
            action="{{ route('admin.creneaux.update', $creneau) }}"
        >
            @csrf
            @method('PATCH')

            <div>
                <label for="date_{{ $creneau->id }}">
                    Date
                </label>

                <input
                    type="date"
                    id="date_{{ $creneau->id }}"
                    name="date"
                    value="{{ $creneau->date->format('Y-m-d') }}"
                >
            </div>

            <div>
                <label for="heure_{{ $creneau->id }}">
                    Heure de début
                </label>

                <input
                    type="time"
                    id="heure_{{ $creneau->id }}"
                    name="heure_debut"
                    value="{{ $creneau->heure_debut->format('H:i') }}"
                >
            </div>

            <div>
                <label for="duree_{{ $creneau->id }}">
                    Durée (minutes)
                </label>

                <input
                    type="number"
                    id="duree_{{ $creneau->id }}"
                    name="duree"
                    value="{{ $creneau->duree }}"
                    min="1"
                >
            </div>

            <button type="submit">
                Modifier
            </button>
        </form>
        <form
    method="POST"
    action="{{ route('admin.creneaux.destroy', $creneau) }}">
    @csrf
    @method('DELETE')

        <button type="submit">
            Supprimer
        </button>
    </form>
        <hr>

    </div>

@empty

    <p>Aucun créneau disponible.</p>

@endforelse
