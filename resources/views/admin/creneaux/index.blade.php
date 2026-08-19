<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                    Gestion des créneaux
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Gérez les créneaux disponibles pour les rendez-vous.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Message de succès --}}
            @if (session('success'))
                <div class="rounded-lg bg-green-50 border border-green-200 p-4 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Erreurs --}}
            @if ($errors->any())
                <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulaire d'ajout --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6">
                    Ajouter un créneau
                </h3>

                <form method="POST" action="{{ route('admin.creneaux.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <div>
                            <label
                                for="date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Date
                            </label>

                            <input
                                type="date"
                                id="date"
                                name="date"
                                value="{{ old('date') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label
                                for="heure_debut"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Heure de début
                            </label>

                            <input
                                type="time"
                                id="heure_debut"
                                name="heure_debut"
                                value="{{ old('heure_debut') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label
                                for="duree"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Durée (minutes)
                            </label>

                            <input
                                type="number"
                                id="duree"
                                name="duree"
                                value="{{ old('duree') }}"
                                min="1"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                    </div>

                    <div class="mt-6">
                        <button
                            type="submit"
                            class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
                        >
                            Ajouter le créneau
                        </button>
                    </div>
                </form>
            </div>

            {{-- Liste des créneaux --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden">

                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        Créneaux existants
                    </h3>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Liste des créneaux actuellement enregistrés.
                    </p>
                </div>

                @forelse ($creneaux as $creneau)

                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 last:border-b-0">

                        {{-- Informations du créneau --}}
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

                                <div>
                                    <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                                        Date
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $creneau->date->format('d/m/Y') }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                                        Heure
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $creneau->heure_debut->format('H:i') }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                                        Durée
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $creneau->duree }} minutes
                                    </p>
                                </div>

                            </div>

                        </div>

                        {{-- Modification --}}
                        <div class="mt-6 rounded-lg bg-gray-50 dark:bg-gray-700/50 p-5">

                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4">
                                Modifier le créneau
                            </h4>

                            <form
                                method="POST"
                                action="{{ route('admin.creneaux.update', $creneau) }}"
                            >
                                @csrf
                                @method('PATCH')

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                    <div>
                                        <label
                                            for="date_{{ $creneau->id }}"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                        >
                                            Date
                                        </label>

                                        <input
                                            type="date"
                                            id="date_{{ $creneau->id }}"
                                            name="date"
                                            value="{{ $creneau->date->format('Y-m-d') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                    </div>

                                    <div>
                                        <label
                                            for="heure_{{ $creneau->id }}"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                        >
                                            Heure de début
                                        </label>

                                        <input
                                            type="time"
                                            id="heure_{{ $creneau->id }}"
                                            name="heure_debut"
                                            value="{{ $creneau->heure_debut->format('H:i') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                    </div>

                                    <div>
                                        <label
                                            for="duree_{{ $creneau->id }}"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                        >
                                            Durée (minutes)
                                        </label>

                                        <input
                                            type="number"
                                            id="duree_{{ $creneau->id }}"
                                            name="duree"
                                            value="{{ $creneau->duree }}"
                                            min="1"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                    </div>

                                </div>

                                <div class="mt-5 flex flex-wrap gap-3">

                                    <button
                                        type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition"
                                    >
                                        Modifier
                                    </button>

                            </form>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.creneaux.destroy', $creneau) }}"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce créneau ?')"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition"
                                        >
                                            Supprimer
                                        </button>
                                    </form>

                                </div>

                        </div>

                    </div>

                @empty

                    <div class="p-8 text-center">
                        <p class="text-gray-500 dark:text-gray-400">
                            Aucun créneau disponible.
                        </p>
                    </div>

                @endforelse

            </div>

        </div>
    </div>
</x-app-layout>
