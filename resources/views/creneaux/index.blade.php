<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Créneaux disponibles
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <p class="mb-4 text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </p>
                    @endif

                    @if ($errors->any())
                        <ul class="mb-4 text-red-600 dark:text-red-400">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    @forelse ($creneaux as $creneau)
                        <div class="mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded">
                            <p>
                                Date : {{ $creneau->date->format('d/m/Y') }}
                            </p>
                            <p>
                                Heure : {{ $creneau->heure_debut->format('H:i') }}
                            </p>
                            <p>
                                Durée : {{ $creneau->duree }} minutes
                            </p>

                            <form
                                method="POST"
                                action="{{ route('rendez-vous.store') }}"
                                class="mt-2"
                            >
                                @csrf
                                <input
                                    type="hidden"
                                    name="creneau_id"
                                    value="{{ $creneau->id }}"
                                >
                                <button type="submit">
                                    Réserver
                                </button>
                            </form>
                        </div>
                    @empty
                        <p>Aucun créneau disponible.</p>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</x-app-layout>